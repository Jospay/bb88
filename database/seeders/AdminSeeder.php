<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admin')->insert([
            'full_name'      => 'Juan Dela Cruz',
            'email'          => 'juandelacruz@gmail.com',
            'contact_number' => '09123123123',
            'password'       => Hash::make('password'),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}
