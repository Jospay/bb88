<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('paymongo-signature');
        $webhookSecret = env('PAYMONGO_WEBHOOK_SECRET');

        // 1. Verify the signature to ensure request is from PayMongo
        if ($signature && $webhookSecret) {
            $splitSignature = explode(',', $signature);
            $timestamp = str_replace('t=', '', $splitSignature[0]);
            $li_signature = str_replace('li=', '', $splitSignature[2] ?? '');

            $baseString = $timestamp . "." . $payload;
            $hash = hash_hmac('sha256', $baseString, $webhookSecret);

            if ($hash !== $li_signature) {
                Log::error("PayMongo Webhook: Invalid Signature detected!");
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $data = json_decode($payload, true);

        // Correct path for the event type
        $type = $data['data']['attributes']['type'] ?? '';

        Log::info("PayMongo Webhook Verified & Received Event: " . $type);

        if ($type === 'checkout_session.payment.paid') {
            $sessionObject = $data['data']['attributes']['data'];

            $sessionId = $sessionObject['id'] ?? null;
            $teamId = $sessionObject['attributes']['metadata']['team_id'] ?? null;

            Log::info("Processing Webhook for Team ID: " . $teamId);

            if ($teamId) {
                $user = User::find($teamId);

                // Only finalize if the user isn't already marked as paid
                if ($user && $user->transaction_status !== 'paid') {
                    (new PaymentController())->finalizeRegistration($user, $sessionId);
                    Log::info("Successfully finalized registration via Webhook for Team: " . $user->team_name);
                }
            } else {
                Log::warning("PayMongo Webhook: No team_id found in metadata.");
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
