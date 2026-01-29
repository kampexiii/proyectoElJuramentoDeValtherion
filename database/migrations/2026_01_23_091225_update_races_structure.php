<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->renameColumn('access', 'min_role');
            $table->unsignedInteger('stat_points_total')->default(25);
            $table->json('caps_json')->nullable();
            $table->json('bonuses_json')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn(['stat_points_total', 'caps_json', 'bonuses_json']);
            $table->renameColumn('min_role', 'access');
        });
    }
};
