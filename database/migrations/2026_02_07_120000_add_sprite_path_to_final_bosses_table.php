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
            $table->string('sprite_path')->nullable()->after('lore');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_bosses', function (Blueprint $table) {
            $table->dropColumn('sprite_path');
        });
    }
};
