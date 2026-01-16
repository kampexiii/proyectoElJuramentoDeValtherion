<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('rarity_id')->constrained('rarities')->restrictOnDelete();
            $table->unsignedInteger('required_level')->default(1)->index();
            $table->boolean('mountable')->default(false);

            $table->unsignedInteger('hp');
            $table->unsignedInteger('strength');
            $table->unsignedInteger('magic');
            $table->unsignedInteger('defense');
            $table->unsignedInteger('speed');

            $table->longText('description');
            $table->timestamps();

            $table->index(['rarity_id']);
        });

        Schema::create('character_animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('animal_id')->constrained('animals')->restrictOnDelete();
            $table->dateTime('acquired_at');
            $table->timestamps();

            $table->unique(['character_id', 'animal_id']);
            $table->index(['animal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_animals');
        Schema::dropIfExists('animals');
    }
};
