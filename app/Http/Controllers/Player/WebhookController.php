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

        // 1. Verify Signature
        if ($signature && $webhookSecret) {
            $splitSignature = explode(',', $signature);
            $timestamp = str_replace('t=', '', $splitSignature[0]);
            $li_signature = str_replace('li=', '', $splitSignature[2] ?? '');

            $baseString = $timestamp . "." . $payload;
            $hash = hash_hmac('sha256', $baseString, $webhookSecret);

            if ($hash !== $li_signature) {
                Log::error("PayMongo Webhook: Invalid Signature!");
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $data = json_decode($payload, true);
        $type = $data['data']['attributes']['type'] ?? '';

        if ($type === 'checkout_session.payment.paid') {
            // PayMongo nesting for Checkout Sessions can be tricky.
            // We pull the resource attributes which contains our metadata.
            $attributes = $data['data']['attributes']['data']['attributes'] ?? null;

            // Fallback for different PayMongo API versions/payload structures
            if (!$attributes) {
                $attributes = $data['data']['attributes'] ?? null;
            }

            $sessionId = $data['data']['attributes']['data']['id'] ?? ($data['data']['id'] ?? null);

            // Look for team_id in metadata
            $teamId = $attributes['metadata']['team_id'] ?? null;

            Log::info("PayMongo Webhook Received", [
                'type' => $type,
                'team_id' => $teamId,
                'session_id' => $sessionId
            ]);

            if ($teamId) {
                $user = User::find($teamId);

                if ($user && $user->transaction_status !== 'paid') {
                    // Execute the finalization (Updates DB and sends Emails)
                    // Note: We instantiate the controller to access the protected method
                    (new PaymentController())->finalizeRegistration($user, $sessionId);

                    Log::info("Webhook successfully processed for Team: " . $user->team_name);
                } else {
                    Log::info("Webhook skipped: User already paid or not found.", ['team_id' => $teamId]);
                }
            } else {
                Log::warning("PayMongo Webhook: No team_id found in metadata.");
            }
        }

        // Always return 200 to stop PayMongo from retrying
        return response()->json(['status' => 'success'], 200);
    }
}
