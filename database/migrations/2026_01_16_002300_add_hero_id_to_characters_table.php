<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->foreignId('hero_id')
                ->nullable()
                ->after('race_id')
                ->constrained('heroes')
                ->nullOnDelete();

            $table->unique(['hero_id']);
            $table->index(['hero_id']);
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropUnique(['hero_id']);
            $table->dropIndex(['hero_id']);
            $table->dropForeign(['hero_id']);
            $table->dropColumn('hero_id');
        });
    }
};
