<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PercentageType;
use App\Models\PercentageBreakdowns;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class PercentageBreakdownsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Fetch all users
        $users = User::all();

        // 2. Fetch all percentage rules (e.g., System Fee 10%, Referral 5%, etc.)
        $percentageTypes = PercentageType::all();

        if ($percentageTypes->isEmpty()) {
            $this->command->error("No PercentageTypes found! Please seed them first.");
            return;
        }

        foreach ($users as $user) {
            // Get the total payment from the users table
            // We use 0 as a fallback if the column is null
            $baseAmount = $user->total_payment ?? 0;

            // If the user has no payments, we skip them or set to 0
            if ($baseAmount <= 0) continue;

            foreach ($percentageTypes as $type) {
                /**
                 * COMPUTATION:
                 * percentage_value = 25 (representing 25%)
                 * calculation = total_payment * (25 / 100)
                 */
                $computedEarning = $baseAmount * ($type->value / 100);

                // Insert into percentage_breakdowns table
                PercentageBreakdowns::create([
                    'user_id'            => $user->id,
                    'percentage_type_id' => $type->id,
                    'total_earning'      => $computedEarning, // This is now the Peso value
                ]);
            }
        }

        $this->command->info("Successfully calculated and seeded breakdowns for all users.");
    }
}
