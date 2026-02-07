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
        Schema::table('final_bosses', function (Blueprint $table) {
            $table->string('slug')->unique();
            $table->longText('lore')->nullable();
            $table->json('base_stats_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_bosses', function (Blueprint $table) {
            $table->dropColumn(['slug', 'lore', 'base_stats_json']);
        });
    }
};
