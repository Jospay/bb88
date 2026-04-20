<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

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

                if (!$attributes) {
                    return response()->json(['status' => 'invalid payload'], 400);
                }

                // Get metadata team_id
                $teamId = $attributes['metadata']['team_id'] ?? null;

                if (!$teamId) {
                    return response()->json(['status' => 'no team_id'], 400);
                }

                $user = User::find($teamId);

                if ($user && $user->transaction_status !== 'paid') {

                    $user->update([
                        'transaction_status' => 'paid'
                    ]);

                    Log::info("Webhook: Payment marked as PAID for Team ID: {$teamId}");
                }
            }

            // ✅ VERY IMPORTANT: Always return 200
            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());

            // Still return 200 to stop retries (optional but recommended)
            return response()->json(['status' => 'error handled'], 200);
        }
    }
}
