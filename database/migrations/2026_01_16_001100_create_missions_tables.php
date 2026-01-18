<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->longText('description');
            $table->unsignedInteger('min_level')->default(1);
            $table->unsignedInteger('base_difficulty');
            $table->unsignedInteger('base_exp');
            $table->unsignedInteger('base_points');
            $table->timestamps();

            $table->index(['min_level']);
        });

        Schema::create('mission_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->string('node_key');
            $table->longText('text');
            $table->boolean('is_end')->default(false);
            $table->timestamps();

            $table->unique(['mission_id', 'node_key']);
            $table->index(['mission_id']);
        });

        Schema::create('mission_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->constrained('mission_nodes')->cascadeOnDelete();
            $table->string('choice_text');
            $table->foreignId('next_node_id')
                ->nullable()
                ->constrained('mission_nodes')
                ->nullOnDelete();

            $table->integer('difficulty_delta')->default(0);
            $table->decimal('exp_multiplier', 6, 2)->default(1.00);
            $table->decimal('points_multiplier', 6, 2)->default(1.00);
            $table->foreignId('loot_table_id')->nullable()->constrained('loot_tables')->nullOnDelete();
            $table->timestamps();

            $table->index(['node_id']);
            $table->index(['next_node_id']);
            $table->index(['loot_table_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_choices');
        Schema::dropIfExists('mission_nodes');
        Schema::dropIfExists('missions');
    }
};
