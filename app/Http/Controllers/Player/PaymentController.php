<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DetailUser;
use App\Models\PercentageType;
use App\Models\PercentageBreakdowns;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        $user = auth()->user();
        Log::info("Payment attempt started for Team: {$user->team_name} (ID: {$user->id})");

        try {
            $session = $this->createPaymongoCheckoutSession(
                $user,
                $user->total_payment,
                $user->email,
                $user->team_name
            );

            $user->update([
                'paymongo_checkout_session_id' => $session['id']
            ]);

            return Inertia::location($session['attributes']['checkout_url']);

        } catch (\Exception $e) {
            Log::error("Payment Initialization Error: " . $e->getMessage());
            return back()->with('error', 'Could not initialize payment. Please try again.');
        }
    }

    protected function createPaymongoCheckoutSession(User $user, $amount, $email, $teamName)
    {
        $formattedAmount = (int)($amount * 100);
        $teamId = $user->id;

        $successUrl = route('player.payment.verify') . '?team_id=' . $teamId;
        $failureUrl = url('/player');

        $payload = [
            'data' => [
                'attributes' => [
                    'billing' => [
                        'name' => $teamName,
                        'email' => trim($email),
                    ],
                    'send_email_receipt' => false,
                    'show_description' => true,
                    'show_line_items' => true,
                    'cancel_url' => $failureUrl,
                    'success_url' => $successUrl,
                    'line_items' => [[
                        'name' => 'Team Registration - ' . $teamName,
                        'amount' => $formattedAmount,
                        'currency' => 'PHP',
                        'quantity' => 1,
                    ]],
                    'payment_method_types' => ['card', 'paymaya', 'qrph', 'billease', 'grab_pay', 'dob'],
                    'description' => 'Team Registration Payment for ' . $teamName,
                    'statement_descriptor' => 'BB88REG',
                    'metadata' => ['team_id' => (string)$teamId],
                ],
            ],
        ];

        $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
            ->post('https://api.paymongo.com/v1/checkout_sessions', $payload);

        if ($response->failed()) {
            throw new \Exception('PayMongo Error: ' . ($response->json()['errors'][0]['detail'] ?? 'Unknown Error'));
        }

        return $response->json()['data'];
    }

    public function handlePaymentSuccess(Request $request)
    {
        $teamId = $request->query('team_id');
        if (!$teamId) return redirect('/player')->with('error', 'Verification failed.');

        $user = User::find($teamId);
        if (!$user) return redirect('/player')->with('error', 'Team not found.');

        // FAST TRACK: If Webhook already finished the work, go to success immediately
        if ($user->transaction_status === 'paid') {
            Log::info("Redirect: User {$user->id} already marked as paid via Webhook. Skipping API call.");
            return redirect()->route('player.payment.success', ['id' => $user->paymongo_checkout_session_id]);
        }

        $sessionId = $user->paymongo_checkout_session_id;

        try {
            // Include payment_intent to verify the actual status
            $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                ->get("https://api.paymongo.com/v1/checkout_sessions/{$sessionId}");

            $sessionData = $response->json()['data'];
            $status = $sessionData['attributes']['status'] ?? 'active';

            // Check if the session is paid or has successful payments attached
            $hasPayment = !empty($sessionData['attributes']['payments']);

            if ($status === 'paid' || ($status === 'active' && $hasPayment)) {
                $this->finalizeRegistration($user, $sessionId);
                return redirect()->route('player.payment.success', ['id' => $sessionId]);
            }

            return redirect('/player')->with('status', 'Payment is still being processed by your provider.');
        } catch (\Exception $e) {
            Log::error("Verification Error: " . $e->getMessage());
            return redirect('/player')->with('error', 'Verification error.');
        }
    }

    public function finalizeRegistration(User $user, $sessionId)
    {
        DB::transaction(function () use ($user, $sessionId) {
            // Re-fetch with lock to prevent race conditions between Redirect and Webhook
            $user = User::lockForUpdate()->find($user->id);

            if ($user->transaction_status === 'paid') return;

            $user->update([
                'transaction_status' => 'paid',
                'paymongo_checkout_session_id' => $sessionId
            ]);

            // Handle earnings distribution
            $percentageTypes = PercentageType::all();
            $baseAmount = $user->total_payment ?? 0;

            if ($percentageTypes->isNotEmpty() && $baseAmount > 0) {
                foreach ($percentageTypes as $type) {
                    PercentageBreakdowns::create([
                        'user_id' => $user->id,
                        'percentage_type_id' => $type->id,
                        'total_earning' => $baseAmount * ($type->value / 100),
                    ]);
                }
            }

            // Trigger Emails
            $this->processNotifications($user);
            Log::info("Finalized Registration for: " . $user->team_name);
        });
    }

    protected function processNotifications(User $user)
    {
        $allDetails = DetailUser::where('user_id', $user->id)->get();
        $currentDate = Carbon::now()->format('F d, Y');

        $htmlBody = $this->createConfirmationEmailBody(
            $user->team_name,
            $currentDate,
            $allDetails->where('account_type', 'Player'),
            $allDetails->where('account_type', 'Reserve'),
            $allDetails->where('account_type', 'Shirt')
        );

        $emails = $allDetails->pluck('email')->filter()
            ->map(fn($e) => strtolower(trim($e)))->unique();

        foreach ($emails as $email) {
            try {
                Mail::html($htmlBody, function ($mail) use ($email, $user) {
                    $mail->to($email)->subject('BB 88 Registration Confirmed: ' . $user->team_name);
                });
            } catch (\Exception $e) {
                Log::error("Mail fail for {$email}: " . $e->getMessage());
            }
        }
    }

    protected function createConfirmationEmailBody(string $teamName, string $date, Collection $playerDetails, Collection $reserveDetails, Collection $shirtDetails): string
{
    $playerListHtml = $playerDetails->isNotEmpty()
        ? $playerDetails->map(fn($d) => '<li>'.htmlspecialchars($d->full_name).'</li>')->implode('')
        : '<li>No players registered.</li>';

    $additionalReserveRow = '';
    if ($reserveDetails->isNotEmpty()) {
        $list = $reserveDetails->map(fn($d) => '<li>'.htmlspecialchars($d->full_name).'</li>')->implode('');
        $additionalReserveRow = '<tr><td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Reserve Players:</strong></td><td style="padding: 10px; border: 1px solid #ddd;"><ul style="margin: 0; padding-left: 20px; list-style-type: decimal;">' . $list . '</ul></td></tr>';
    }

    $additionalShirtRow = '';
    if ($shirtDetails->isNotEmpty()) {
        $list = $shirtDetails->map(fn($d) => '<li>'.htmlspecialchars($d->full_name).'</li>')->implode('');
        $additionalShirtRow = '<tr><td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Additional Shirts:</strong></td><td style="padding: 10px; border: 1px solid #ddd;"><ul style="margin: 0; padding-left: 20px; list-style-type: decimal;">' . $list . '</ul></td></tr>';
    }

    return '<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
    <h2 style="color: #4CAF50; text-align: center;">Congratulations! Registration Confirmed!</h2>
    <hr style="border: 0; border-top: 2px solid #eee;">
    <p>Dear Captain of <strong>' . htmlspecialchars($teamName) . '</strong>,</p>
    <p>We are thrilled to confirm your team\'s successful registration and payment for the BB 88 Advertising & Digital Solution Inc. event!</p>
    <table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
        <tr><td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; width: 35%;"><strong>Team Name:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($teamName) . '</td></tr>
        <tr><td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Registration Date:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($date) . '</td></tr>
        <tr><td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Status:</strong></td><td style="padding: 10px; border: 1px solid #ddd;"><strong style="color: #4CAF50;">PAID & CONFIRMED</strong></td></tr>
        <tr><td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Main Players:</strong></td><td style="padding: 10px; border: 1px solid #ddd;"><ul style="margin: 0; padding-left: 20px; list-style-type: decimal;">' . $playerListHtml . '</ul></td></tr>
        ' . $additionalReserveRow . '
        ' . $additionalShirtRow . '
    </table>
    <div style="background-color: #e6f7ff; padding: 15px; border-left: 5px solid #007bff; margin: 25px 0;">
        <strong style="color: #0056b3;">Next Steps:</strong>
        <ul style="margin-top: 10px; margin-bottom: 10px;">
            <li>Further instructions and updates will be sent via email or SMS.</li>
            <li>Your team members who processed the payment may log in to access the QR code, which will be used for claiming team shirts.</li>
            <li>
                To access your account, click this link:
                <strong style="color: #007bff;">
                    <a href="https://mobaebz.bb88advertising.com/login" target="_blank" style="color: #007bff; text-decoration: underline;">
                        https://mobaebz.bb88advertising.com/login
                    </a>
                </strong>.
                Use your team email.
            </li>
            <li>Please wait for the official announcement regarding QR code activation, schedules, and claiming details.</li>
        </ul>
    </div>
    <p>Thank you for registering! We look forward to seeing you at the event.</p>
    <p style="margin-top: 30px; font-size: 0.9em; color: #777;">Best regards,<br>The BB 88 Advertising & Digital Solution Inc. Team</p>
    </div>';
}
}
