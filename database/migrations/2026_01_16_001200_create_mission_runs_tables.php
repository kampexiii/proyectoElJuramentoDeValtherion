<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();

            $table->unsignedInteger('final_difficulty');
            $table->unsignedInteger('exp_gained');
            $table->unsignedInteger('points_gained');
            $table->boolean('completed')->default(false);
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->timestamps();

            $table->index(['mission_id']);
            $table->index(['character_id']);
            $table->index(['season_id']);
        });

        Schema::create('mission_run_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_run_id')->constrained('mission_runs')->cascadeOnDelete();
            $table->foreignId('node_id')->constrained('mission_nodes')->restrictOnDelete();
            $table->foreignId('choice_id')->constrained('mission_choices')->restrictOnDelete();
            $table->unsignedInteger('step_number');
            $table->timestamps();

            $table->index(['mission_run_id']);
            $table->index(['step_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_run_choices');
        Schema::dropIfExists('mission_runs');
    }
};
