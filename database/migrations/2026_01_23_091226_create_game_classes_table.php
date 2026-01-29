<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('min_role')->default('free')->index();
            $table->json('bonuses_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_classes');
    }
};
