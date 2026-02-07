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
        Schema::create('character_mission_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('mission_id')->constrained('missions');
            $table->enum('status', ['active', 'boss_pending', 'completed', 'abandoned', 'failed'])->index();
            $table->foreignId('current_node_id')->nullable()->constrained('mission_nodes')->nullOnDelete();
            $table->unsignedTinyInteger('current_step_index')->default(1);
            $table->integer('danger_score')->default(0);
            $table->integer('wound_stacks')->default(0);
            $table->timestamp('rewards_applied_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->index(['character_id', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_mission_runs');
    }
};
