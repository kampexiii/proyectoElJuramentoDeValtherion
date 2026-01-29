<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('characters')) {
            return;
        }

        Schema::table('characters', function (Blueprint $table) {
            if (!Schema::hasColumn('characters', 'hp_max')) {
                $table->unsignedInteger('hp_max')->default(0)->after('stats_json');
            }
            if (!Schema::hasColumn('characters', 'hp_current')) {
                $table->unsignedInteger('hp_current')->default(0)->after('hp_max');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('characters')) {
            return;
        }

        Schema::table('characters', function (Blueprint $table) {
            if (Schema::hasColumn('characters', 'hp_current')) {
                $table->dropColumn('hp_current');
            }
            if (Schema::hasColumn('characters', 'hp_max')) {
                $table->dropColumn('hp_max');
            }
        });
    }
};
