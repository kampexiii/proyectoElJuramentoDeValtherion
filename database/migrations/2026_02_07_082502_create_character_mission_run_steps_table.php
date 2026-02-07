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
        Schema::create('character_mission_run_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('run_id')->constrained('character_mission_runs')->cascadeOnDelete();
            $table->unsignedTinyInteger('step_index');
            $table->foreignId('node_id')->constrained('mission_nodes');
            $table->foreignId('choice_id')->constrained('mission_choices');
            $table->unsignedTinyInteger('difficulty_points_snapshot');
            $table->json('effects_snapshot_json')->nullable();
            $table->unique(['run_id', 'step_index']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_mission_run_steps');
    }
};
