<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loot_tables', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('rolls')->default(1);
            $table->unsignedInteger('min_level')->nullable();
            $table->timestamps();
        });

        Schema::create('loot_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loot_table_id')->constrained('loot_tables')->cascadeOnDelete();

            $table->string('entry_type');
            $table->foreignId('item_id')->nullable()->constrained('items')->restrictOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('animals')->restrictOnDelete();

            $table->unsignedInteger('weight');
            $table->unsignedInteger('min_qty')->default(1);
            $table->unsignedInteger('max_qty')->default(1);
            $table->unsignedInteger('required_level')->nullable();
            $table->timestamps();

            $table->index(['loot_table_id']);
            $table->index(['entry_type']);
            $table->index(['item_id']);
            $table->index(['animal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loot_entries');
        Schema::dropIfExists('loot_tables');
    }
};
