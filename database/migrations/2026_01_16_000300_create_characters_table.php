<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('race_id')->constrained()->restrictOnDelete();

            $table->string('name')->unique();

            $table->unsignedInteger('level')->default(1);
            $table->unsignedBigInteger('exp')->default(0);
            $table->unsignedBigInteger('gold')->default(0);

            $table->integer('hp_bonus')->default(0);
            $table->integer('strength_bonus')->default(0);
            $table->integer('magic_bonus')->default(0);
            $table->integer('defense_bonus')->default(0);
            $table->integer('speed_bonus')->default(0);

            $table->timestamps();
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->index(['user_id']);
            $table->index(['race_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
