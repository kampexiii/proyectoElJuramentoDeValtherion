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
        Schema::table('character_mission_runs', function (Blueprint $table) {
            $table->timestamp('partial_xp_awarded_at')->nullable()->after('rewards_applied_at');
            $table->integer('partial_xp_amount')->default(0)->after('partial_xp_awarded_at');
            $table->timestamp('abandoned_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('character_mission_runs', function (Blueprint $table) {
            $table->dropColumn(['partial_xp_awarded_at', 'partial_xp_amount', 'abandoned_at']);
        });
    }
};
