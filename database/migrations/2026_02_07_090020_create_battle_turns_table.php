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
        Schema::create('battle_turns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('battle_id')->constrained('battles')->cascadeOnDelete();
            $table->unsignedInteger('turn_number');
            $table->string('p1_action');
            $table->string('p2_action');
            $table->enum('first_actor', ['p1', 'p2']);
            $table->integer('damage_to_p1')->default(0);
            $table->integer('damage_to_p2')->default(0);
            $table->json('notes_json')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battle_turns');
    }
};
