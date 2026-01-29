<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('access')->default('free')->index();
            $table->longText('lore')->nullable();

            $table->unsignedInteger('base_hp');
            $table->unsignedInteger('base_strength');
            $table->unsignedInteger('base_magic');
            $table->unsignedInteger('base_defense');
            $table->unsignedInteger('base_speed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('races');
    }
};
