<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('items')) {
            return;
        }

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable()->index();
            $table->unsignedInteger('required_level')->default(1);
            $table->boolean('stackable')->default(false);
            $table->unsignedInteger('sell_price')->default(0);

            $table->integer('bonus_hp')->default(0);
            $table->integer('bonus_strength')->default(0);
            $table->integer('bonus_magic')->default(0);
            $table->integer('bonus_defense')->default(0);
            $table->integer('bonus_speed')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('items')) {
            return;
        }

        Schema::dropIfExists('items');
    }
};
