<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\DetailUser;
use App\Models\PercentageType;
use App\Models\PercentageBreakdowns;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

// Load QR library
if (!class_exists('QRcode')) {
    if (file_exists(base_path('phpqrcode/qrlib.php'))) {
        require_once base_path('phpqrcode/qrlib.php');
    } else {
        Log::error('phpqrcode library not found at: ' . base_path('phpqrcode/qrlib.php'));
    }
}

class RegistrationController extends Controller
{
    // Keeping your path structure
    const QR_CODE_PATH = 'qr_image/';

    // UPDATED: SUCCESS_URL now points to our new dedicated verification route
    // const SUCCESS_URL = 'https://bb88.test/payment/verify';
    // const FAILURE_URL = 'https://bb88.test/payment/failure';

    const SUCCESS_URL = 'https://mobaebz.bb88advertising.com/payment/verify';
    const FAILURE_URL = 'https://mobaebz.bb88advertising.com/payment/failure';

    protected function sendMoviderSms(string $recipient, string $message): void
    {
        // ... (Movider SMS function remains unchanged)
        try {
            $apiKey = "laPHOJ7nEDtXgMDplPgt5eZWCHxrv2";
            $apiSecret = "bXNW6-H5BzdCpbgncRMj-MDJ_8sZ9L";
            $senderId = "BB88";
            $apiUrl = 'https://rest.movider.co/campaign'; // Campaign API

            if (empty($apiKey) || empty($apiSecret)) {
                Log::error('Movider API credentials are not set in the environment.');
                return;
            }

            // IMPROVED NUMBER FORMATTING FIX: Ensure the number starts with 63.
            $cleanedRecipient = preg_replace('/[^0-9]/', '', $recipient);

            // Case 1: 09XXXXXXXXX (11 digits, starts with 0) -> 639XXXXXXXXX
            if (strlen($cleanedRecipient) == 11 && substr($cleanedRecipient, 0, 1) === '0') {
                $formattedRecipient = '63' . substr($cleanedRecipient, 1);
            }
            // Case 2: 9XXXXXXXXX (10 digits, starts with 9) -> 639XXXXXXXXX
            elseif (strlen($cleanedRecipient) == 10 && substr($cleanedRecipient, 0, 1) === '9') {
                $formattedRecipient = '63' . $cleanedRecipient;
            }
            // Case 3: 639XXXXXXXXX (12 digits, already correct) or other non-PH format
            else {
                // Use it as is, trusting it's already in the correct international format (e.g., 639xxxxxxxxx)
                $formattedRecipient = $cleanedRecipient;
            }

            // CAMPAIGN NAME FIX: Use the requested format "07 Dec 2025 | 20:41:21"
            $now = Carbon::now();
            $campaignDate = $now->format('d M Y'); // e.g., 07 Dec 2025
            $campaignTime = $now->format('H:i:s'); // e.g., 20:41:21

            // Generate a unique campaign name for logging/tracking purposes
            $uniqueCampaignName = "{$campaignDate} | {$campaignTime} | BB88Reg"; // NEW FORMAT HERE

            // CAMPAIGN API PAYLOAD STRUCTURE
            $payload = [
                // Use the formatted number
                'phoneNumbers' => ['phone' => [$formattedRecipient]],
                'campaignName' => $uniqueCampaignName,
                'senderName' => $senderId,
                'message' => $message,
            ];

            // Use Basic Auth for Movider Campaign API
            $response = Http::withBasicAuth($apiKey, $apiSecret)
                ->post($apiUrl, $payload);

            if ($response->failed()) {
                // Log the formatted number and the full response body for better debugging
                Log::error('Movider CAMPAIGN SMS failed to send to ' . $formattedRecipient . '. HTTP Status: ' . $response->status() . '. Response: ' . $response->body());
            } else {
                Log::info('Movider CAMPAIGN SMS sent successfully to ' . $formattedRecipient . '. Campaign ID: ' . ($response->json()['campaignID'] ?? 'N/A'));
            }

        } catch (\Exception $e) {
            Log::error('Movider CAMPAIGN SMS Exception: ' . $e->getMessage());
        }
    }


    /**
     * Creates a PayMongo Checkout Session and returns its details.
     */
    protected function createPaymongoCheckoutSession(User $user, $amount, $email, $teamName)
    {
        $formattedAmount = (int)($amount * 100);
        $teamId = $user->id;

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
                    'cancel_url' => self::FAILURE_URL . '?team_id=' . $teamId,
                    'success_url' => self::SUCCESS_URL . '?team_id=' . $teamId,
                    'line_items' => [[
                        'name' => 'Team Registration - ' . $teamName,
                        'amount' => $formattedAmount,
                        'currency' => 'PHP',
                        'quantity' => 1,
                    ]],
                    'payment_method_types' => ['card', 'paymaya', 'qrph', 'billease', 'grab_pay', 'dob'],
                    'description' => 'Team Registration Payment for ' . $teamName,
                    'statement_descriptor' => 'BB88REG',
                    'metadata' => ['team_id' => $teamId],
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

    /**
     * NEW HELPER FUNCTION: Checks if any submitted detail belongs to an UNPAID team.
     * @param array $detailUsers Array of submission details.
     * @return User|null The existing unpaid User model, or null if none found.
     */


    protected function checkExistingUnpaidTeam(array $detailUsers): ?User
{
    // 1. Filter out 'Shirt' types or 'N/A' usernames
    // Now explicitly allowing 'Reserve' type to be part of the duplicate check logic
    $validPlayers = collect($detailUsers)->filter(function($player) {
        return isset($player['username']) &&
               $player['username'] !== 'N/A' &&
               $player['username'] !== '' &&
               in_array($player['accountType'], ['Player', 'Reserve']);
    });

    if ($validPlayers->isEmpty()) {
        return null;
    }

    // 2. Build a query that looks for a specific player matching ALL 3 criteria
    $detailRecords = DetailUser::where(function($query) use ($validPlayers) {
        foreach ($validPlayers as $player) {
            $query->orWhere(function($q) use ($player) {
                $q->where('email', $player['email'])
                  ->where('username', $player['username'])
                  ->where('mobile_number', $player['mobileNumber']);
            });
        }
    })->get();

    if ($detailRecords->isEmpty()) {
        return null;
    }

    // 3. Get the team ID and ensure they haven't paid yet
    $userIds = $detailRecords->pluck('user_id')->unique()->toArray();

    return User::whereIn('id', $userIds)
        ->where('transaction_status', '!=', 'paid')
        ->first();
}

public function register(Request $request)
{
    $teamData = $request->input('team');
    $detailUsers = $request->input('details', []);

    // --- SANITIZATION: Force null for Shirt usernames, preserve others ---
    foreach ($detailUsers as $key => $detail) {
        if (isset($detail['accountType']) && $detail['accountType'] === 'Shirt') {
            $detailUsers[$key]['username'] = null;
        }
    }
    $request->merge(['details' => $detailUsers]);

    $captainEmail = $detailUsers[0]['email'] ?? 'unknown@example.com';

    // --- STEP 1: CHECK FOR EXISTING UNPAID TEAM ---
    $existingUser = $this->checkExistingUnpaidTeam($detailUsers);

    if ($existingUser) {
        Log::info("Existing UNPAID team found (ID: {$existingUser->id}).");

        $validator = Validator::make($request->all(), [
            'team.team_name' => 'required|string|max:255|unique:users,team_name,' . $existingUser->id,
            'team.total_payment' => 'required|numeric|min:2500',
            'team.agree' => 'accepted',
            'details.0.email' => 'required|email',
            'team.region' => 'required|string',
            'team.province' => 'required|string',
            'team.city' => 'required|string',
            'team.barangay' => 'required|string',
            'team.postal_code' => 'required|string',
        ], [
            'team.agree.accepted' => 'You must agree to the Privacy Policy to proceed.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $captainEmail = $request->input('details.0.email');

        $existingUser->fill([
            'team_name' => $teamData['team_name'],
            'total_payment' => $teamData['total_payment'],
            'additional_shirt_count' => $teamData['additional_shirt_count'],
            'region' => $teamData['region'],
            'province' => $teamData['province'] ?? null,
            'city' => $teamData['city'],
            'barangay' => $teamData['barangay'] ?? null,
            'postal_code' => $teamData['postal_code'] ?? null,
        ]);

        DB::beginTransaction();
        try {
            $existingUser->save();

            $sessionData = $this->createPaymongoCheckoutSession(
                $existingUser,
                $teamData['total_payment'],
                $captainEmail,
                $teamData['team_name']
            );

            $existingUser->update([
                'paymongo_checkout_session_id' => $sessionData['id'],
                'transaction_status' => 'pending_payment',
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Existing team found. Redirecting to payment.',
                'user_id' => $existingUser->id,
                'checkout_url' => $sessionData['attributes']['checkout_url']
            ], 202);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("PayMongo Session Error (Existing Team): " . $e->getMessage());
            return response()->json([
                'message' => 'Team re-registration failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // --- STEP 2: NORMAL CREATION ---
    $validator = Validator::make($request->all(), [
        'team.team_name' => 'required|string|max:255|unique:users,team_name',
        'team.total_payment' => 'required|numeric|min:2500',
        'team.agree' => 'accepted',
        'team.additional_shirt_count' => 'required|integer|min:0',
        'team.region' => 'required|string',
        'team.province' => 'required|string',
        'team.city' => 'required|string',
        'team.barangay' => 'required|string',
        'team.postal_code' => 'required|string',
        'details' => 'required|array|min:5',
        'details.*.fullName' => 'required|string|max:255',
        'details.*.username' => 'nullable|string|max:255|unique:detail_user,username',
        'details.*.email' => 'required|email|unique:detail_user,email',
        'details.*.mobileNumber' => 'required|string|digits:10|unique:detail_user,mobile_number',
        'details.*.accountType' => 'required|in:Player,Shirt,Reserve', // Added Reserve here
    ], [
            'team.agree.accepted' => 'You must agree to the Privacy Policy to proceed.'
        ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $captainEmail = $request->input('details.0.email');

    DB::beginTransaction();
    try {
        $user = User::create([
            'team_name' => $teamData['team_name'],
            'total_payment' => $teamData['total_payment'],
            'additional_shirt_count' => $teamData['additional_shirt_count'],
            'country' => $teamData['country'] ?? 'Philippines',
            'region' => $teamData['region'],
            'province' => $teamData['province'] ?? null,
            'city' => $teamData['city'],
            'barangay' => $teamData['barangay'] ?? null,
            'postal_code' => $teamData['postal_code'] ?? null,
            'transaction_status' => 'pending_registration',
        ]);

        $sessionData = $this->createPaymongoCheckoutSession($user, $teamData['total_payment'], $captainEmail, $teamData['team_name']);

        $user->update([
            'paymongo_checkout_session_id' => $sessionData['id'],
            'transaction_status' => 'pending_payment',
        ]);

        // --- QR CODE LOGIC ---
        if (class_exists('QRcode')) {
            $lastDetail = DetailUser::orderBy('id', 'desc')->lockForUpdate()->first();
            $lastNumber = 0;
            if ($lastDetail && preg_match('/BB88(\d+)/', $lastDetail->qrcode_img, $matches)) {
                $lastNumber = (int)$matches[1];
            }

            $qrCodeDirectory = public_path(self::QR_CODE_PATH);
            if (!is_dir($qrCodeDirectory)) mkdir($qrCodeDirectory, 0755, true);

            foreach ($detailUsers as $detail) {
                $lastNumber++;
                $sequential = str_pad($lastNumber, 6, '0', STR_PAD_LEFT);
                $qrPlain = 'BB88' . $sequential;
                $qrImg = $qrPlain . '.png';
                $qrHashed = Hash::make($qrPlain);

                $insertedDetail = DetailUser::create([
                    'user_id' => $user->id,
                    'full_name' => $detail['fullName'],
                    'username' => $detail['username'],
                    'email' => $detail['email'],
                    'mobile_number' => $detail['mobileNumber'],
                    'account_type' => $detail['accountType'],
                    'qrcode_name' => $qrHashed,
                    'qrcode_img' => $qrImg,
                ]);

                if ($insertedDetail) {
                    \QRcode::png($qrHashed, $qrCodeDirectory . $qrImg, QR_ECLEVEL_L, 10, 2);
                } else {
                    throw new \Exception("Failed to insert DetailUser record.");
                }
            }
        }

        DB::commit();
        return response()->json([
            'message' => 'Team registered. Redirecting to payment.',
            'user_id' => $user->id,
            'checkout_url' => $sessionData['attributes']['checkout_url']
        ], 202);

    } catch (\Exception $e) {
        DB::rollback();
        Log::error("Registration Failed: " . $e->getMessage());
        return response()->json(['message' => 'Registration failed.', 'error' => $e->getMessage()], 500);
    }
}


    /**
     * Handles the PayMongo success callback to verify and update payment status.
     * FIX: Prevents duplicate processing and sends emails to ALL unique recipients (Players and Shirts).
     */
    public function handlePaymentSuccess(Request $request)
{
    $teamId = $request->query('team_id');
    if (!$teamId) {
        return redirect('/register')->with('error', 'Payment verification failed: Missing team ID.');
    }

    // 1. Find the User with pessimistic lock to prevent race conditions
    $user = User::lockForUpdate()->find($teamId);

    if (!$user) {
        return redirect('/register')->with('error', 'Payment verification failed: Team not found.');
    }

    // Prevent processing if already marked as paid
    if ($user->transaction_status === 'paid') {
         Log::info("Team ID {$teamId} already paid. Skipping duplicate notification process.");
         return redirect('/payment/success?id=' . $user->paymongo_checkout_session_id);
    }

    $sessionId = $user->paymongo_checkout_session_id;

    if (!$sessionId) {
        Log::error("PayMongo Verification Error: Session ID missing for Team ID {$teamId}.");
        return redirect('/register')->with('error', 'Payment session not recorded. Please register again.');
    }

    try {
        DB::beginTransaction();

        // 2. Fetch PayMongo Session Status
        $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
            ->get("https://api.paymongo.com/v1/checkout_sessions/{$sessionId}?include=payment_intent");

        if ($response->failed()) {
            throw new \Exception('Failed to fetch PayMongo session details.');
        }

        $sessionData = $response->json()['data'];
        $paymentStatus = $sessionData['attributes']['payment_intent']['attributes']['status'] ?? 'pending';

        // 3. Update User Status Based on Payment Intent Status
        if ($paymentStatus === 'succeeded' || $paymentStatus === 'paid') {
            $user->update(['transaction_status' => 'paid']);

            // --- 🚀 START: DISTRIBUTION OF BREAKDOWN (Inserted only once paid) ---
            $percentageTypes = PercentageType::all();
            $baseAmount = $user->total_payment ?? 0;

            if ($percentageTypes->isNotEmpty() && $baseAmount > 0) {
                foreach ($percentageTypes as $type) {
                    // Calculation: total_payment * (percentage_value / 100)
                    $computedEarning = $baseAmount * ($type->value / 100);

                    PercentageBreakdowns::create([
                        'user_id'            => $user->id,
                        'percentage_type_id' => $type->id,
                        'total_earning'      => $computedEarning,
                    ]);
                }
                Log::info("Earnings breakdown created for Team ID {$user->id}. Total Base: {$baseAmount}");
            }
            // --- END: DISTRIBUTION OF BREAKDOWN ---

            // 📢 Fetch ALL DetailUser records for the team
            $allDetails = DetailUser::where('user_id', $user->id)->get();
            $currentDate = Carbon::now()->format('F d, Y');
            $playerDetails = $allDetails->where('account_type', 'Player');
            $reserveDetails = $allDetails->where('account_type', 'Reserve');
            $shirtDetails = $allDetails->where('account_type', 'Shirt');

            // Prepare the designed HTML email content
            $htmlBody = $this->createConfirmationEmailBody($user->team_name, $currentDate, $playerDetails, $reserveDetails, $shirtDetails);

            // --- SMS De-duplication and Content Logic ---
            $sentNumbers = [];
            $smsCount = 0;

            if ($allDetails->isNotEmpty()) {
                foreach ($allDetails as $detail) {
                    $rawMobileNumber = $detail->mobile_number;
                    $accountType = $detail->account_type;
                    $cleanedRecipient = preg_replace('/[^0-9]/', '', $rawMobileNumber);

                    if (strlen($cleanedRecipient) == 11 && substr($cleanedRecipient, 0, 1) === '0') {
                        $formattedMobileNumber = '63' . substr($cleanedRecipient, 1);
                    } elseif (strlen($cleanedRecipient) == 10 && substr($cleanedRecipient, 0, 1) === '9') {
                        $formattedMobileNumber = '63' . $cleanedRecipient;
                    } else {
                        $formattedMobileNumber = $cleanedRecipient;
                    }

                    if (in_array($formattedMobileNumber, $sentNumbers)) continue;

                    // $actionText = ($accountType === 'Player') ? "registered as a Player. Claim your shirt at any event redemption center" : ($accountType === 'Shirt') ? "registered for an additional Shirt. Claim your shirt at any event redemption center"
                    // : "registered for a Reserved Player";
                    // $smsMessage = "Congrats! Team {$user->team_name} is registered. You are {$actionText}. Use the Cinco app to login and show your QR code for claiming. Thank you!";

                    // 1. Determine the descriptive role/action based on account type
                    $label = 'Claim your shirt at any event redemption center. Use the Cinco app to login and show your QR code for claiming';
                    $actionText = match($accountType) {
                        'Player'  => "registered as a Player. {$label}",
                        'Shirt'   => "registered for an additional Shirt. {$label}",
                        'Reserve' => "registered as a Reserve Player. Use the Cinco app to login",
                        default   => "registered"
                    };

                    // 2. Build the final message
                    $smsMessage = "Congrats! Team {$user->team_name} is registered. You are {$actionText}. Thank you!";

                    $this->sendMoviderSms($rawMobileNumber, $smsMessage);
                    $sentNumbers[] = $formattedMobileNumber;
                    $smsCount++;
                }

                // 📧 Send Custom HTML Email to ALL unique emails
                $allUniqueEmails = $allDetails->pluck('email')->unique();
                foreach ($allUniqueEmails as $recipientEmail) {
                    try {
                        Mail::html($htmlBody, function ($mail) use ($recipientEmail, $user) {
                            $mail->to($recipientEmail)
                                ->subject('BB 88 Registration Confirmed: ' . $user->team_name);
                        });
                    } catch (\Exception $e) {
                        Log::error("Failed to send email to {$recipientEmail}: " . $e->getMessage());
                    }
                }
            }

            DB::commit(); // Commit status update and breakdowns
            return redirect('/payment/success?id=' . $sessionId);

        } elseif ($paymentStatus === 'pending') {
            $user->update(['transaction_status' => 'pending']);
            DB::commit();
            return redirect('/register')->with('status', 'Payment status is still pending.');

        } else {
            $user->update(['transaction_status' => 'failed']);
            DB::commit();
            return redirect('/register')->with('error', 'Payment failed or was cancelled.');
        }

    } catch (\Exception $e) {
        DB::rollback(); // Rollback if any error occurs
        Log::error("PayMongo Verification Error for Team ID {$teamId}: " . $e->getMessage());
        return redirect('/register')->with('error', 'An error occurred during payment verification.');
    }
}

    /**
     * Generates a simple, designed HTML body for the confirmation email.
     * This is a basic inline CSS design for maximum compatibility.
     */

    protected function createConfirmationEmailBody(string $teamName, string $date, Collection $playerDetails, Collection $reserveDetails, Collection $shirtDetails): string
{
    // 1. Generate list items for MAIN PLAYERS
    $playerListHtml = '';
    if ($playerDetails->isNotEmpty()) {
        foreach ($playerDetails as $index => $detail) {
            $playerListHtml .= '<li>' . htmlspecialchars($detail->full_name) . '</li>';
        }
    } else {
        $playerListHtml = '<li>No players registered (Team only registered for shirts).</li>';
    }

    // 2. Generate list items for RESERVE PLAYERS
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

    // 3. Generate list items for ADDITIONAL SHIRTS
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
        <h2 style="color: #4CAF50; text-align: center;">Congratulations! Registration Confirmed!</h2>
        <hr style="border: 0; border-top: 2px solid #eee;">

        <p>Dear Captain of <strong>' . htmlspecialchars($teamName) . '</strong>,</p>

        <p>We are thrilled to confirm your team\'s successful registration and payment for the BB 88 Advertising & Digital Solution Inc. event!</p>

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
                <td style="padding: 10px; border: 1px solid #ddd;"><strong style="color: #4CAF50;">PAID & CONFIRMED</strong></td>
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
            <strong style="color: #0056b3;">Next Steps:</strong>
            <ul style="margin-top: 10px; margin-bottom: 10px;">
                <li>Your team\'s QR codes have been generated.</li>
                <li>Claim your team shirts at any designated event redemption center.</li>
                <li>Please use the official <strong>Cinco App</strong> to log in and show your unique QR code for verification during the event and shirt claiming.</li>
            </ul>

            <strong style="color: #0056b3;">Important Notes:</strong>
            <ul style="margin-top: 10px;">
                <li>Every add-on shirt is worth <strong>₱ 500.00</strong>. Purchasing an add-on shirt does not grant eligibility to play.</li>
                <li>The 5 listed main players automatically receive a shirt included in the base registration fee.</li>
                <li><strong>Reserve Players:</strong> Please note that reserve players do not receive a shirt by default. A reserve player will only receive a shirt and be eligible to play if one of the 5 main players backs out.</li>
            </ul>
        </div>

        <p>Thank you for registering! We look forward to seeing you at the event.</p>

        <p style="margin-top: 30px; font-size: 0.9em; color: #777;">
            Best regards,<br>
            The BB 88 Advertising & Digital Solution Inc. Team
        </p>
    </div>';
}
}
