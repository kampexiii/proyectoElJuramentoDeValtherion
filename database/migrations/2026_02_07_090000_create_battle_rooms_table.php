<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('battle_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('guest_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['open', 'in_progress', 'finished', 'closed'])->index();
            $table->unsignedBigInteger('battle_id')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->index('owner_user_id');
            $table->index('guest_user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battle_rooms');
    }
};
