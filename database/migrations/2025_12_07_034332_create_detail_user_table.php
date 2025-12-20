<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_user', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key linking to the 'users' table
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // User Profile Columns
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('mobile_number')->nullable()->unique();
            $table->string('password')->nullable();
            $table->enum('account_type', ['Player', 'Shirt'])->nullable();
            $table->string('qrcode_name')->nullable();
            $table->string('qrcode_img')->nullable();
            $table->integer('verification_account')->default(0);
            $table->enum('status', ['pending', 'claimed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_user');
    }
};
