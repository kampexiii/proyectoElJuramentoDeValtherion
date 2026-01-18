<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code_hash')->unique();
            $table->unsignedInteger('max_uses');
            $table->unsignedInteger('uses_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['is_active']);
        });

        Schema::create('premium_code_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('premium_code_id')->constrained('premium_codes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('redeemed_at');
            $table->timestamps();

            $table->unique(['premium_code_id', 'user_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_code_redemptions');
        Schema::dropIfExists('premium_codes');
    }
};
