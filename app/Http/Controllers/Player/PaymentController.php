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
use Inertia\Inertia; // Added for Inertia::location

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

            Log::info("PayMongo Session Created: " . $session['id']);

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

        $successUrl = url('/player/payment/success') . '?team_id=' . $teamId;
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
            Log::error("PayMongo API Failure: " . $response->body());
            throw new \Exception('PayMongo Error: ' . ($response->json()['errors'][0]['detail'] ?? 'Unknown Error'));
        }

        return $response->json()['data'];
    }


    public function handlePaymentSuccess(Request $request)
    {
        $teamId = $request->query('team_id');

        if (!$teamId) {
            return redirect('/player')->with('error', 'Payment verification failed.');
        }

        $user = User::find($teamId);

        if (!$user) {
            return redirect('/player')->with('error', 'Team not found.');
        }

        $sessionId = $user->paymongo_checkout_session_id;

        // ✅ IMPORTANT: STOP DUPLICATE EXECUTION
        if ($user->transaction_status === 'paid') {
            return redirect()->route('player.payment.success', ['id' => $sessionId]);
        }

        if (!$sessionId) {
            return redirect('/player')->with('error', 'Missing session ID.');
        }

        try {
            DB::beginTransaction();

            $user = User::lockForUpdate()->find($teamId);

            $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                ->get("https://api.paymongo.com/v1/checkout_sessions/{$sessionId}?include=payment_intent");

            $responseData = $response->json();

            if (!isset($responseData['data'])) {
                throw new \Exception('Invalid PayMongo response.');
            }

            $sessionData = $responseData['data'];

            $paymentIntent = $sessionData['attributes']['payment_intent'] ?? null;
            $paymentStatus = $paymentIntent['attributes']['status'] ?? 'pending';

            $statusMap = [
                'succeeded' => 'paid',
                'paid' => 'paid',
                'processing' => 'pending_payment',
                'awaiting_payment_method' => 'pending_payment',
                'pending' => 'pending_payment',
            ];

            $finalStatus = $statusMap[$paymentStatus] ?? 'pending_payment';

            if ($finalStatus === 'paid') {

                // ✅ CHECK AGAIN (double safety)
                if ($user->transaction_status !== 'paid') {

                    $user->update(['transaction_status' => 'paid']);

                    // earnings
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

                    // ✅ SEND EMAIL ONLY ONCE
                    $this->processNotifications($user);
                }

                DB::commit();

                return redirect()->route('player.payment.success', ['id' => $sessionId]);

            } else {

                $user->update(['transaction_status' => $finalStatus]);

                DB::commit();

                return redirect('/player')->with('status', "Payment status: $finalStatus.");
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/player')->with('error', 'Verification error.');
        }
    }

    protected function processNotifications(User $user)
    {
        $allDetails = DetailUser::where('user_id', $user->id)->get();
        $currentDate = Carbon::now()->format('F d, Y');

        $playerDetails = $allDetails->where('account_type', 'Player');
        $reserveDetails = $allDetails->where('account_type', 'Reserve');
        $shirtDetails = $allDetails->where('account_type', 'Shirt');

        $htmlBody = $this->createConfirmationEmailBody(
            $user->team_name,
            $currentDate,
            $playerDetails,
            $reserveDetails,
            $shirtDetails
        );

        // ✅ STRONG UNIQUE (fix duplicates even if spacing/case issue)
        $allUniqueEmails = $allDetails
            ->pluck('email')
            ->filter()
            ->map(fn($email) => strtolower(trim($email)))
            ->unique()
            ->values();

        foreach ($allUniqueEmails as $recipientEmail) {
            Mail::html($htmlBody, function ($mail) use ($recipientEmail, $user) {
                $mail->to($recipientEmail)
                    ->subject('BB 88 Registration Confirmed: ' . $user->team_name);
            });
        }
    }

    // DESIGN KEPT EXACTLY THE SAME
    protected function createConfirmationEmailBody(string $teamName, string $date, Collection $playerDetails, Collection $reserveDetails, Collection $shirtDetails): string
    {
        $playerListHtml = '';
        if ($playerDetails->isNotEmpty()) {
            foreach ($playerDetails as $detail) {
                $playerListHtml .= '<li>' . htmlspecialchars($detail->full_name) . '</li>';
            }
        } else {
            $playerListHtml = '<li>No players registered.</li>';
        }

        $reserveListHtml = '';
        $additionalReserveRow = '';
        if ($reserveDetails->isNotEmpty()) {
            foreach ($reserveDetails as $detail) {
                $reserveListHtml .= '<li>' . htmlspecialchars($detail->full_name) . '</li>';
            }
            $additionalReserveRow = '<tr><td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Reserve Players:</strong></td><td style="padding: 10px; border: 1px solid #ddd;"><ul style="margin: 0; padding-left: 20px; list-style-type: decimal;">' . $reserveListHtml . '</ul></td></tr>';
        }

        $shirtListHtml = '';
        $additionalShirtRow = '';
        if ($shirtDetails->isNotEmpty()) {
            foreach ($shirtDetails as $detail) {
                $shirtListHtml .= '<li>' . htmlspecialchars($detail->full_name) . '</li>';
            }
            $additionalShirtRow = '<tr><td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Additional Shirts:</strong></td><td style="padding: 10px; border: 1px solid #ddd;"><ul style="margin: 0; padding-left: 20px; list-style-type: decimal;">' . $shirtListHtml . '</ul></td></tr>';
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
                <li>Your team\'s QR codes have been generated.</li>
                <li>Claim your team shirts at any designated event redemption center.</li>
                <li>Please use the official <strong>Cinco App</strong> to log in and show your unique QR code for verification during the event and shirt claiming.</li>
            </ul>
        </div>
        <p>Thank you for registering! We look forward to seeing you at the event.</p>
        <p style="margin-top: 30px; font-size: 0.9em; color: #777;">Best regards,<br>The BB 88 Advertising & Digital Solution Inc. Team</p>
        </div>';
    }
}
