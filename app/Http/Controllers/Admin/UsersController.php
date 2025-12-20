<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailUser;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index()
    {
        // Fetch all users from the detail_user table
        $users = DetailUser::with('user')->get();

        return Inertia::render('dashboard/Users', [
            'users' => $users
        ]);
    }
}
