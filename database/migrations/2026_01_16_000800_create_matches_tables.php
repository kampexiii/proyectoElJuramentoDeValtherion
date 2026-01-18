<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('ongoing')->index();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->timestamps();
        });

        Schema::create('match_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->boolean('is_winner')->default(false);
            $table->dateTime('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['match_id', 'character_id']);
            $table->index(['match_id']);
            $table->index(['character_id']);
        });

        Schema::create('match_turns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->unsignedInteger('turn_number');
            $table->foreignId('acting_participant_id')
                ->constrained('match_participants')
                ->cascadeOnDelete();
            $table->longText('action_text');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['match_id', 'turn_number']);
            $table->index(['match_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_turns');
        Schema::dropIfExists('match_participants');
        Schema::dropIfExists('matches');
    }
};
