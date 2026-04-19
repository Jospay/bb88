<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\DetailUser;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function showLoginForm() {
        if (Auth::guard('player')->check()) {
            return redirect()->route('player.index');
        }
        // Match the new PascalCase path
        return Inertia::render('auth/login/PlayerLogin');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 1. Find the Detail record by email
        $detail = DetailUser::where('email', $credentials['email'])->first();

        // 2. Check if detail exists AND password matches
        if ($detail && Hash::check($credentials['password'], $detail->password)) {

            // 3. Log in the parent User model using the 'player' guard
            Auth::guard('player')->login($detail->user);

            $request->session()->regenerate();

            return redirect()->intended('/player');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request) {
        Auth::guard('player')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
