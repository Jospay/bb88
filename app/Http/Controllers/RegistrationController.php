<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\DetailUser;
use App\Models\PercentageType;
use App\Models\PercentageBreakdowns;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

if (!class_exists('QRcode')) {
    if (file_exists(base_path('phpqrcode/qrlib.php'))) {
        require_once base_path('phpqrcode/qrlib.php');
    } else {
        Log::error('phpqrcode library not found at: ' . base_path('phpqrcode/qrlib.php'));
    }
}

class RegistrationController extends Controller
{
    const QR_CODE_PATH = 'qr_image/';

    public function register(Request $request)
{
    $teamData = $request->input('team');
    $detailUsers = $request->input('details', []);

    // Force username = null for Shirt
    foreach ($detailUsers as $key => $detail) {
        if (isset($detail['accountType']) && $detail['accountType'] === 'Shirt') {
            $detailUsers[$key]['username'] = null;
        }
    }

    $request->merge(['details' => $detailUsers]);

    $validator = Validator::make($request->all(), [
        'team.team_name' => 'required|string|max:255|unique:users,team_name',
        'team.agree' => 'accepted',
        'team.total_payment' => 'required|numeric',
        'team.region' => 'required|string',
        'team.province' => 'required|string',
        'team.city' => 'required|string',
        'team.barangay' => 'required|string',
        'team.postal_code' => 'required|string',

        'details' => 'required|array|min:1',
        'details.*.fullName' => 'required|string|max:255',

        // ✅ Changed: now nullable
        'details.*.username' => 'nullable|string|max:255',

        'details.*.email' => 'required|email|unique:detail_user,email',
        'details.*.mobileNumber' => 'required|string|digits:10|unique:detail_user,mobile_number',
        'details.*.size_shirt' => 'required|string',
        'details.*.accountType' => 'required|in:Player,Shirt,Reserve',
    ], [
        'team.agree.accepted' => 'The Privacy Policy must be accepted to proceed.'
    ]);

    // ✅ ADD THIS BLOCK (IMPORTANT FIX)
    $validator->after(function ($validator) use ($detailUsers) {
        foreach ($detailUsers as $index => $detail) {
            // Only require username for Player & Reserve
            if (in_array($detail['accountType'], ['Player', 'Reserve'])) {
                if (empty($detail['username'])) {
                    $validator->errors()->add(
                        "details.$index.username",
                        "The username field is required."
                    );
                }
            }
        }
    });

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $uniqueToken = Str::random(40);
    $user = null;

    DB::beginTransaction();
    try {
        $user = User::create([
            'team_name' => $teamData['team_name'],
            'total_payment' => $teamData['total_payment'],
            'additional_shirt_count' => $teamData['additional_shirt_count'] ?? 0,
            'country' => $teamData['country'] ?? 'Philippines',
            'region' => $teamData['region'],
            'province' => $teamData['province'] ?? null,
            'city' => $teamData['city'],
            'barangay' => $teamData['barangay'] ?? null,
            'postal_code' => $teamData['postal_code'] ?? null,
            'token' => $uniqueToken,
            'transaction_status' => 'pending_registration',
        ]);

        $percentageTypes = PercentageType::all();
        foreach ($percentageTypes as $type) {
            PercentageBreakdowns::create([
                'user_id' => $user->id,
                'percentage_type_id' => $type->id,
                'total_earning' => $user->total_payment * ($type->value / 100),
            ]);
        }

        $qrCodeDirectory = public_path(self::QR_CODE_PATH);
        if (!is_dir($qrCodeDirectory)) mkdir($qrCodeDirectory, 0755, true);

        $lastDetail = DetailUser::orderBy('id', 'desc')->first();
        $lastNumber = ($lastDetail && preg_match('/BB88(\d+)/', $lastDetail->qrcode_img, $matches)) ? (int)$matches[1] : 0;

        foreach ($detailUsers as $detail) {
            $lastNumber++;
            $qrPlain = 'BB88' . str_pad($lastNumber, 6, '0', STR_PAD_LEFT);
            $qrImg = $qrPlain . '.png';
            $qrHashed = Str::uuid()->toString();

            DetailUser::create([
                'user_id' => $user->id,
                'full_name' => $detail['fullName'],
                'username' => $detail['username'] ?? null,
                'email' => $detail['email'],
                'mobile_number' => $detail['mobileNumber'],
                'account_type' => $detail['accountType'],
                'size_shirt' => $detail['size_shirt'],
                'qrcode_name' => $qrHashed,
                'qrcode_img' => $qrImg,
            ]);

            if (class_exists('QRcode')) {
                \QRcode::png($qrHashed, $qrCodeDirectory . $qrImg, QR_ECLEVEL_L, 10, 2);
            }
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        Log::error("Registration Failed: " . $e->getMessage());
        return response()->json([
            'message' => 'Registration failed.',
            'error' => $e->getMessage()
        ], 500);
    }

    if ($user) {
        $this->processNotifications($user);
    }

    return response()->json([
        'message' => 'Registration successful.',
        'token' => $uniqueToken
    ], 200);
}

    protected function processNotifications(User $user)
    {
        $allDetails = DetailUser::where('user_id', $user->id)->get();
        $currentDate = Carbon::now()->format('F d, Y');
        // $formattedAmount = number_format($user->total_payment, 2);

        // $sentNumbers = [];
        // foreach ($allDetails as $detail) {
        //     if (in_array($detail->mobile_number, $sentNumbers)) continue;

        //     // Updated label to include the specific amount and "full team payment" phrasing
        //     $label = "Total payment to be paid is ₱{$formattedAmount} after the team qualifies, a separate email will be sent with instructions for the full team payment.";

        //     $actionText = match ($detail->account_type) {
        //         'Player'  => "registered as a Player. {$label}",
        //         'Shirt'   => "registered for an additional Shirt. {$label}",
        //         'Reserve' => "registered as a Reserve Player. Please wait for the qualification email.",
        //         default   => "registered"
        //     };

        //     $this->sendMoviderSms($detail->mobile_number, "Congrats! Team {$user->team_name} is registered. You are {$actionText}");
        //     $sentNumbers[] = $detail->mobile_number;
        // }

        $htmlBody = $this->createConfirmationEmailBody(
            $user->team_name,
            $currentDate,
            $allDetails->where('account_type', 'Player'),
            $allDetails->where('account_type', 'Reserve'),
            $allDetails->where('account_type', 'Shirt'),
            $user->total_payment
        );

        foreach ($allDetails->pluck('email')->unique() as $recipientEmail) {
            try {
                Mail::html($htmlBody, function ($mail) use ($recipientEmail, $user) {
                    $mail->to($recipientEmail)->subject('Registration Received: ' . $user->team_name);
                });
            } catch (\Exception $e) {
                Log::error("Email failed for {$recipientEmail}: " . $e->getMessage());
            }
        }
    }

    protected function createConfirmationEmailBody(string $teamName, string $date, Collection $playerDetails, Collection $reserveDetails, Collection $shirtDetails, $totalPayment): string
    {
        $playerListHtml = '';
        if ($playerDetails->isNotEmpty()) {
            foreach ($playerDetails as $index => $detail) {
                $playerListHtml .= '<li>' . htmlspecialchars($detail->full_name) . '</li>';
            }
        } else {
            $playerListHtml = '<li>No players registered (Team only registered for shirts).</li>';
        }

        $reserveListHtml = '';
        $additionalReserveRow = '';
        if ($reserveDetails->isNotEmpty()) {
            foreach ($reserveDetails as $index => $detail) {
                $reserveListHtml .= '<li>' . htmlspecialchars($detail->full_name) . '</li>';
            }
            $additionalReserveRow = '
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Reserve Players:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">
                    <ul style="margin: 0; padding-left: 20px; list-style-type: decimal;">
                        ' . $reserveListHtml . '
                    </ul>
                </td>
            </tr>';
        }

        $shirtListHtml = '';
        $additionalShirtRow = '';
        if ($shirtDetails->isNotEmpty()) {
            foreach ($shirtDetails as $index => $detail) {
                $shirtListHtml .= '<li>' . htmlspecialchars($detail->full_name) . '</li>';
            }
            $additionalShirtRow = '
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Additional Shirts:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">
                    <ul style="margin: 0; padding-left: 20px; list-style-type: decimal;">
                        ' . $shirtListHtml . '
                    </ul>
                </td>
            </tr>';
        }

        return '<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
        <h2 style="color: #4CAF50; text-align: center;">Congratulations! Registration Received!</h2>
        <hr style="border: 0; border-top: 2px solid #eee;">

        <p>Dear Captain of <strong>' . htmlspecialchars($teamName) . '</strong>,</p>

        <p>We are thrilled to confirm that your team\'s registration for the BB 88 Advertising & Digital Solution Inc. event has been successfully received!</p>

        <table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; width: 35%;"><strong>Team Name:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($teamName) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Registration Date:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($date) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Status:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong style="color: #007bff;">PENDING QUALIFICATION</strong></td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Main Players:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">
                    <ul style="margin: 0; padding-left: 20px; list-style-type: decimal;">
                        ' . $playerListHtml . '
                    </ul>
                </td>
            </tr>
            ' . $additionalReserveRow . '
            ' . $additionalShirtRow . '
        </table>

        <div style="background-color: #e6f7ff; padding: 15px; border-left: 5px solid #007bff; margin: 25px 0;">
            <strong style="color: #0056b3;">Important Notes:</strong>
            <ul style="margin-top: 10px; margin-bottom: 10px;">
                <li><strong>No payment is required right now.</strong></li>
                <li>total payment to be paid is ₱' . number_format($totalPayment, 2) . ' after the team qualifies, a separate email will be sent with instructions for the full team payment.</li>
            </ul>
        </div>

        <p>Thank you for registering! We wish your team the best of luck in the qualification phase.</p>

        <p style="margin-top: 30px; font-size: 0.9em; color: #777;">
            Best regards,<br>
            The BB 88 Advertising & Digital Solution Inc. Team
        </p>
    </div>';
    }
}
