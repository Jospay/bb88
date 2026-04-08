<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('detailUser')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('dashboard/Users', [
            'users' => $users
        ]);
    }
}
