<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('id');
            $table->string('slot')->nullable()->after('type'); 
            
            $table->renameColumn('sell_price', 'value_gold');
            
            $table->boolean('is_consumable')->default(false)->after('required_level');
            $table->integer('max_stack')->default(1)->after('stackable');
            
            $table->json('bonuses_json')->nullable();
            $table->json('effects_json')->nullable();
            
            $table->dropColumn(['bonus_hp', 'bonus_strength', 'bonus_magic', 'bonus_defense', 'bonus_speed']);
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
             $table->integer('bonus_hp')->default(0);
             $table->integer('bonus_strength')->default(0);
             $table->integer('bonus_magic')->default(0);
             $table->integer('bonus_defense')->default(0);
             $table->integer('bonus_speed')->default(0);
             
             $table->dropColumn(['bonuses_json', 'effects_json', 'max_stack', 'is_consumable', 'slot', 'code']);
             $table->renameColumn('value_gold', 'sell_price');
        });
    }
};
