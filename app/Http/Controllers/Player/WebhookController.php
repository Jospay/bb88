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
            // Resource data is inside attributes -> data
            $resource = $data['data']['attributes']['data'];
            $sessionId = $resource['id'];
            $teamId = $resource['attributes']['metadata']['team_id'] ?? null;

            if ($teamId) {
                $user = User::find($teamId);
                if ($user && $user->transaction_status !== 'paid') {
                    // This runs the DB update and sends the EMAIL
                    (new PaymentController())->finalizeRegistration($user, $sessionId);
                    Log::info("Webhook success for Team: " . $user->team_name);
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
