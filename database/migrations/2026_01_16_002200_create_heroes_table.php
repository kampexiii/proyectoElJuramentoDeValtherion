<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heroes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('race_id')->constrained('races')->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->longText('description');

            $table->unsignedInteger('base_hp');
            $table->unsignedInteger('base_strength');
            $table->unsignedInteger('base_magic');
            $table->unsignedInteger('base_defense');
            $table->unsignedInteger('base_speed');

            $table->boolean('unique_global')->default(true);
            $table->timestamps();

            $table->index(['race_id']);
            $table->index(['unique_global']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
};
