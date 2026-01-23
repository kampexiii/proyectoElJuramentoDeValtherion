<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            // Drop non-unique index if exists (default name from index(['user_id']))
            $table->dropIndex('characters_user_id_index'); 
            $table->unique('user_id');

            $table->foreignId('game_class_id')->nullable()->after('race_id')->constrained('game_classes')->restrictOnDelete();
            $table->json('stats_json')->nullable()->after('game_class_id');
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('stats_json');
            $table->dropForeign(['game_class_id']);
            $table->dropColumn('game_class_id');
            
            $table->dropUnique(['user_id']);
            $table->index('user_id', 'characters_user_id_index');
        });
    }
};
