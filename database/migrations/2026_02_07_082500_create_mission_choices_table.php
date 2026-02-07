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
        Schema::create('mission_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_node_id')->constrained('mission_nodes')->cascadeOnDelete();
            $table->string('choice_text');
            $table->text('outcome_text')->nullable();
            $table->unsignedTinyInteger('difficulty_points')->default(0);
            $table->json('effects_json')->nullable();
            $table->foreignId('next_node_id')->nullable()->constrained('mission_nodes')->nullOnDelete();
            $table->boolean('goes_to_boss')->default(false);
            $table->unsignedTinyInteger('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_choices');
    }
};
