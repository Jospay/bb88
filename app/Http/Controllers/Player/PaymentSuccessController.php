<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class PaymentSuccessController extends Controller
{
    public function success(Request $request)
    {
        $sessionId = $request->query('id');
        $user = User::where('paymongo_checkout_session_id', $sessionId)->first();

        if (!$user || $user->transaction_status !== 'paid') {
            return redirect('/player');
        }

        return Inertia::render('player/Success', [
            'sessionId' => $sessionId,
            'teamName' => $user->team_name,
        ]);
    }
}
