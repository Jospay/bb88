<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Http\Controllers\Player\PaymentController;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('PayMongo Webhook Received', $payload);

            // Get event type
            $eventType = $payload['data']['attributes']['type'] ?? null;

            if ($eventType === 'checkout_session.payment.paid') {

                $attributes = $payload['data']['attributes']['data']['attributes'] ?? null;
                $sessionId = $payload['data']['attributes']['data']['id'] ?? null;

                if (!$attributes) {
                    return response()->json(['status' => 'invalid payload'], 400);
                }

                // Get metadata team_id
                $teamId = $attributes['metadata']['team_id'] ?? null;

                if (!$teamId) {
                    return response()->json(['status' => 'no team_id'], 400);
                }

                $user = User::find($teamId);

                // Use the logic from PaymentController to avoid code duplication
                if ($user && $user->transaction_status !== 'paid') {

                    (new PaymentController())->finalizeRegistration($user, $sessionId);

                    Log::info("Webhook: Full Registration Finalized for Team ID: {$teamId}");
                }
            }

            // ✅ ALWAYS return 200 to stop PayMongo retries
            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error handled'], 200);
        }
    }
}
