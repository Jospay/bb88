<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PercentageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('percentage_types')->insert([
            [
                'name'       => 'tax',
                'type'       => 'Percentage',
                'value'      => 25,
            ],
            [
                'name'       => 'bank',
                'type'       => 'Percentage',
                'value'      => 25,
            ],
            [
                'name'       => 'markup_fee',
                'type'       => 'Percentage',
                'value'      => 25,
            ],
            [
                'name'       => 'system_fee',
                'type'       => 'Percentage',
                'value'      => 25,
            ],
        ]);
    }
}
