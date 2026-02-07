<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('final_boss_id')->nullable()->constrained('final_bosses')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('intro_text');
            $table->longText('context_text')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->index();
            $table->boolean('repeatable')->default(false);
            $table->integer('base_race_points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
