<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('transaction_status', [
                'pending_registration',
                'pending_payment',
                'paid',
                'failed'
            ])
            ->default('pending_registration')
            ->change();
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
