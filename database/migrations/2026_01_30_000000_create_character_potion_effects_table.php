<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_potion_effects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->enum('effect_type', ['heal_lock', 'stat_boost']);
            $table->string('stat')->nullable(); // strength, magic, defense, speed
            $table->integer('bonus')->nullable()->default(1);
            $table->unsignedInteger('remaining_missions')->default(1);
            $table->timestamps();

            $table->index(['character_id', 'effect_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_potion_effects');
    }
};
