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
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained('battle_rooms')->nullOnDelete();
            $table->enum('type', ['pvp', 'pve']);
            $table->enum('status', ['active', 'finished']);
            $table->foreignId('player1_character_id')->constrained('characters');
            $table->foreignId('player2_character_id')->nullable()->constrained('characters')->nullOnDelete();
            $table->foreignId('final_boss_id')->nullable()->constrained('final_bosses')->nullOnDelete();
            $table->foreignId('mission_run_id')->nullable()->constrained('character_mission_runs')->nullOnDelete();
            $table->unsignedInteger('turn_number')->default(1);
            $table->integer('p1_hp');
            $table->integer('p2_hp');
            $table->boolean('p1_defending')->default(false);
            $table->boolean('p2_defending')->default(false);
            $table->string('pending_p1_action')->nullable();
            $table->string('pending_p2_action')->nullable();
            $table->json('stats_p1_json');
            $table->json('stats_p2_json');
            $table->enum('result', ['p1_win', 'p2_win', 'draw'])->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::table('battle_rooms', function (Blueprint $table) {
            $table->foreign('battle_id')->references('id')->on('battles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battle_rooms', function (Blueprint $table) {
            $table->dropForeign(['battle_id']);
        });

        Schema::dropIfExists('battles');
    }
};
