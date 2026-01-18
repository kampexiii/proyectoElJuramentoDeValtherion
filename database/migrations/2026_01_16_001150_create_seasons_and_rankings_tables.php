<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('year');
            $table->unsignedInteger('month');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->timestamps();

            $table->unique(['year', 'month']);
        });

        Schema::create('season_race_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->unsignedInteger('points')->default(0);
            $table->timestamps();

            $table->unique(['season_id', 'race_id', 'character_id']);
            $table->index(['season_id', 'race_id', 'points']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_race_rankings');
        Schema::dropIfExists('seasons');
    }
};
