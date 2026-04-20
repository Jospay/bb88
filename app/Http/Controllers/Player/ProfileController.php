<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit()
    {
        return Inertia::render('player/Profile', [
            'player' => Auth::guard('player')->user(),
        ]);
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password:player'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()
            ],
        ]);

        $player = Auth::guard('player')->user();
        $player->update([
            'password' => Hash::make($request->password),
        ]);

        // Changed 'message' to 'success' to match Inertia flash props
        return back()->with('success', 'Password changed successfully!');
    }
}
