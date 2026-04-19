<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentSuccessController extends Controller
{
    public function success(Request $request)
    {
        $sessionId = $request->query('id');
        $teamId = $request->query('team_id');
        $authId = auth('player')->id();

        if (!$sessionId && $teamId) {
            $sessionId = User::where('id', $teamId)
                ->where('id', $authId)
                ->value('paymongo_checkout_session_id');
        }

        if (!$sessionId) {
            return redirect('/player')->with('error', 'Payment session ID missing.');
        }

        $user = User::where('paymongo_checkout_session_id', $sessionId)
            ->where('id', $authId)
            ->first();

        if (!$user) {
            return redirect('/player')->with('error', 'Unauthorized access.');
        }

        if ($user->transaction_status !== 'paid') {
            return redirect()->route('player.payment.verify', ['team_id' => $user->id]);
        }

        return Inertia::render('player/Success', [
            'sessionId' => $sessionId,
            'teamName' => $user->team_name,
        ]);
    }
}
