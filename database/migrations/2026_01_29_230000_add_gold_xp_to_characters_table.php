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
            if (!Schema::hasColumn('characters', 'gold')) {
                $table->unsignedInteger('gold')->default(0)->after('stats_json');
            }
            if (!Schema::hasColumn('characters', 'xp')) {
                $table->unsignedInteger('xp')->default(0)->after('gold');
            }
            if (!Schema::hasColumn('characters', 'level')) {
                $table->unsignedInteger('level')->default(1)->after('xp');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('characters')) {
            return;
        }

        Schema::table('characters', function (Blueprint $table) {
            if (Schema::hasColumn('characters', 'level')) {
                $table->dropColumn('level');
            }
            if (Schema::hasColumn('characters', 'xp')) {
                $table->dropColumn('xp');
            }
            if (Schema::hasColumn('characters', 'gold')) {
                $table->dropColumn('gold');
            }
        });
    }
};
