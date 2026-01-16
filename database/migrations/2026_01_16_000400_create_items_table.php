<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type');
            $table->foreignId('rarity_id')->constrained('rarities')->restrictOnDelete();
            $table->unsignedInteger('required_level')->default(1)->index();
            $table->boolean('stackable')->default(false);

            $table->integer('bonus_hp')->default(0);
            $table->integer('bonus_strength')->default(0);
            $table->integer('bonus_magic')->default(0);
            $table->integer('bonus_defense')->default(0);
            $table->integer('bonus_speed')->default(0);

            $table->unsignedInteger('sell_price')->default(0);
            $table->timestamps();

            $table->index(['rarity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
