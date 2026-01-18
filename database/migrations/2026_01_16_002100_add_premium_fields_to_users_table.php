<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('plan')->default('free')->after('password');
            $table->foreignId('premium_granted_by_code_id')
                ->nullable()
                ->after('plan')
                ->constrained('premium_codes')
                ->nullOnDelete();
            $table->dateTime('premium_granted_at')->nullable()->after('premium_granted_by_code_id');

            $table->index(['plan']);
            $table->index(['premium_granted_by_code_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['plan']);
            $table->dropIndex(['premium_granted_by_code_id']);

            $table->dropForeign(['premium_granted_by_code_id']);
            $table->dropColumn('premium_granted_at');
            $table->dropColumn('premium_granted_by_code_id');
            $table->dropColumn('plan');
        });
    }
};
