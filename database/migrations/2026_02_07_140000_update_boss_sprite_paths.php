<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('final_bosses')) {
            return;
        }

        $bosses = DB::table('final_bosses')->select('id', 'slug')->get();
        foreach ($bosses as $boss) {
            DB::table('final_bosses')
                ->where('id', $boss->id)
                ->update(['sprite_path' => '/assets/sprites/bosses/' . $boss->slug . '.png']);
        }
    }

    public function down(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('final_bosses')) {
            return;
        }

        $bosses = DB::table('final_bosses')->select('id', 'slug')->get();
        foreach ($bosses as $boss) {
            DB::table('final_bosses')
                ->where('id', $boss->id)
                ->update(['sprite_path' => '/assets/bosses/' . $boss->slug . '.png']);
        }
    }
};
