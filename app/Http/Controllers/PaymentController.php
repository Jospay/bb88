<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        // Fetch using the unique token instead of the ID
        $token = $request->query('token');

        if (!$token) {
            return redirect('/register')->with('error', 'Registration token missing.');
        }

        $user = User::where('token', $token)
            ->where('transaction_status', 'pending_registration')
            ->select('id', 'team_name', 'token')
            ->first();

        if (!$user) {
            return redirect('/register')->with('error', 'Registration records not found.');
        }

        return Inertia::render('auth/payment/Success', [
            'token'    => $user->token,
            'teamName' => $user->team_name,
        ]);
    }
}
