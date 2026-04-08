<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailUser;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Players (Only from 'paid' transactions)
        $totalPlayers = DetailUser::whereHas('user', function ($query) {
            $query->where('transaction_status', 'paid');
        })->count();

        // 2. Total Shirts (Paid teams + their additional shirts)
        $paidUsers = User::where('transaction_status', 'paid')->get();
        $totalShirts = $paidUsers->count() + $paidUsers->sum('additional_shirt_count');

        // 3. Total Earnings
        $totalEarnings = $paidUsers->sum('total_payment');

        // 4. Latest 10 Registered TEAMS
        $latestTeams = User::withCount('detailUser')
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('dashboard/Index', [
            'stats' => [
                'total_players' => $totalPlayers,
                'total_shirts' => $totalShirts,
                'total_earnings' => number_format($totalEarnings, 2),
            ],
            'latest_teams' => $latestTeams
        ]);
    }
}
