<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    // Show profile edit page
    public function edit()
    {
        return Inertia::render('dashboard/Profile', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }

    // Update profile info
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('admin', 'email')->ignore($admin->id, 'id'),
            ],
            'contact_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('admin', 'contact_number')->ignore($admin->id, 'id'),
            ],
        ]);

        $admin->update($validated);

        return back()->with('message', 'Profile successfully updated!');
    }

    // Update password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $admin = $request->user('admin');
        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('message', 'Password changed successfully!');
    }
}
