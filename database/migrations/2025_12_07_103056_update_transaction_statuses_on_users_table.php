<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // CRITICAL FIX: Update the ENUM column to include all statuses used in your controller.
            $table->enum('transaction_status', [
                'pending_registration', // Status used on initial creation
                'pending_payment',      // Status used after PayMongo session creation
                'paid',                 // Status used on payment success
                'failed'                // Status used on payment failure
            ])
            ->default('pending_registration') // Setting the default to the first logical status
            ->change(); // The 'change()' method applies the modification to the existing column
        });
    }

    public function down(): void
    {
        // Revert the change if the migration is rolled back
        Schema::table('users', function (Blueprint $table) {
            $table->enum('transaction_status', ['pending', 'paid'])
                  ->default('pending')
                  ->change();
        });
    }
};
