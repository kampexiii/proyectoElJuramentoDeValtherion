<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->foreignId('active_animal_id')
                ->nullable()
                ->after('speed_bonus')
                ->constrained('animals')
                ->nullOnDelete();

            $table->index(['active_animal_id']);
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropForeign(['active_animal_id']);
            $table->dropIndex(['active_animal_id']);
            $table->dropColumn('active_animal_id');
        });
    }
};
