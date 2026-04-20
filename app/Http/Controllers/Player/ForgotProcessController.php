<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth; // Important for auto-login
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Mail\PasswordResetMail;

class ForgotProcessController extends Controller
{
    protected $table = 'detail_user';

    public function forgot(Request $request)
    {
        if ($request->isMethod('get')) {
            return Inertia::render('auth/ForgotPassword');
        }

        $request->validate([
            'email' => "required|email|exists:{$this->table},email"
        ], [
            'email.exists' => 'We could not find a player with that email address.'
        ]);

        $generatedToken = Str::random(64);

        DB::table($this->table)
            ->where('email', $request->email)
            ->update(['token' => $generatedToken]);

        $verificationLink = route('player.create.password', ['token' => $generatedToken]);

        // Sending real email via SMTP
        Mail::to($request->email)->send(new PasswordResetMail($verificationLink));

        return back()->with('status', 'A reset link has been sent to your email address. Please check your inbox.');
    }

    public function createPassword(Request $request)
    {
        if ($request->isMethod('get')) {
            return Inertia::render('auth/CreatePassword', ['token' => $request->token]);
        }

        $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // 1. Find the player using the token
        $player = DB::table($this->table)->where('token', $request->token)->first();

        if (!$player) {
            return back()->withErrors(['token' => 'This password reset link is invalid or has expired.']);
        }

        // 2. Update the password and clear the token
        DB::table($this->table)
            ->where('token', $request->token)
            ->update([
                'password' => Hash::make($request->password),
                'token' => null
            ]);
        Auth::guard('player')->loginUsingId($player->user_id);

        return back();
    }
}
