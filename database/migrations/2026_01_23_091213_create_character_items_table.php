<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('character_items')) {
            return;
        }

        Schema::create('character_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['character_id', 'item_id']);
            $table->index(['item_id']);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('character_items')) {
            return;
        }

        Schema::dropIfExists('character_items');
    }
};
