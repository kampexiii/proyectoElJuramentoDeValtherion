<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('loot_table_id')->constrained('loot_tables')->restrictOnDelete();
            $table->unsignedInteger('items_count')->default(3);
            $table->timestamps();
        });

        Schema::create('season_race_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();
            $table->foreignId('winner_character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('chest_id')->constrained('chests')->restrictOnDelete();
            $table->dateTime('granted_at');
            $table->dateTime('claimed_at')->nullable();
            $table->timestamps();

            $table->unique(['season_id', 'race_id']);
            $table->index(['season_id']);
            $table->index(['race_id']);
        });

        Schema::create('chest_openings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chest_id')->constrained('chests')->restrictOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->dateTime('opened_at');
            $table->timestamps();

            $table->index(['chest_id']);
            $table->index(['character_id']);
        });

        Schema::create('chest_opening_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chest_opening_id')->constrained('chest_openings')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->index(['chest_opening_id']);
            $table->index(['item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chest_opening_rewards');
        Schema::dropIfExists('chest_openings');
        Schema::dropIfExists('season_race_winners');
        Schema::dropIfExists('chests');
    }
};
