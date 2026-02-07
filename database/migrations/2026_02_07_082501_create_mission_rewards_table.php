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
        Schema::create('mission_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->unique()->constrained('missions')->cascadeOnDelete();
            $table->integer('xp')->default(0);
            $table->integer('gold')->default(0);
            $table->json('items_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_rewards');
    }
};
