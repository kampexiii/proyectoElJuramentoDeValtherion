<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('character_equipment', function (Blueprint $table) {
            $table->dropForeign(['slot_id']);
            $table->dropUnique(['character_id', 'slot_id']);
            $table->dropColumn('slot_id');

            $table->string('slot')->after('character_id');
            $table->foreignId('item_id')->nullable()->change();

            $table->unique(['character_id', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::table('character_equipment', function (Blueprint $table) {
            $table->dropUnique(['character_id', 'slot']);
            $table->dropColumn('slot');
            
            $table->foreignId('slot_id')->constrained('equipment_slots');
            $table->foreignId('item_id')->nullable(false)->change();
            
            $table->unique(['character_id', 'slot_id']);
        });
    }
};
