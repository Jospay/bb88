<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the authenticated player (User model)
        // We load the detailUser relationship to show the team members
        $user = Auth::guard('player')->user()->load('detailUser');

        return Inertia::render('player/Index', [
            'team' => $user,
        ]);
    }
}
