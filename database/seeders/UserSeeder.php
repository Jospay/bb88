<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['pending_registration', 'pending_payment', 'paid', 'failed'];

        for ($i = 1; $i <= 50; $i++) {
            DB::table('users')->insert([
                'team_name' => 'Team ' . Str::random(5) . ' ' . $i,
                'total_payment' => rand(1000, 5000),
                'additional_shirt_count' => rand(0, 10),
                'country' => 'Philippines',
                'region' => 'Region III',
                'province' => 'Pampanga',
                'city' => 'Angeles City',
                'barangay' => 'Balibago',
                'postal_code' => '2009',
                // Pick a random status from your NEW enum list
                'transaction_status' => $statuses[array_rand($statuses)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
