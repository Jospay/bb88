<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailUser;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index()
    {
        // Use latest() to get the newest entries first
        // latest() is a shorthand for orderBy('created_at', 'desc')
        $users = DetailUser::with('user')
            ->latest()
            ->get();

        return Inertia::render('dashboard/Users', [
            'users' => $users
        ]);
    }
}
