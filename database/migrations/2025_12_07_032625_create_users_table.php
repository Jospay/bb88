<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Location Columns
            $table->string('team_name')->nullable()->unique();
            $table->decimal('total_payment', 8, 2);
            $table->integer('additional_shirt_count')->default(0);

            $table->string('country')->default('Philippines');
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('barangay')->nullable();
            $table->string('postal_code')->nullable();

            $table->string('paymongo_checkout_session_id')->nullable();
            $table->enum('transaction_status', ['pending_registration','pending_payment','paid','failed'])->default('pending_registration');

            // Timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
