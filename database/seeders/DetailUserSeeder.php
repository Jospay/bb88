<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DetailUserSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id')->toArray();

        foreach ($userIds as $id) {
            DB::table('detail_user')->insert([
                'user_id' => $id,
                'full_name' => 'User Test ' . $id,
                'email' => 'testuser' . $id . '@example.com',
                'mobile_number' => '09' . rand(100000000, 999999999),
                'password' => Hash::make('password'),
                'account_type' => ($id % 2 == 0) ? 'Player' : 'Shirt',
                'qrcode_name' => 'QR_CODE_' . $id,
                'qrcode_img' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=User' . $id,
                'verification_account' => rand(0, 1),
                'status' => ($id % 3 == 0) ? 'claimed' : 'pending', // lowercase to match enum
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
