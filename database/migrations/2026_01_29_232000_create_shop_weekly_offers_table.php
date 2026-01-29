<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('shop_weekly_offers')) {
            return;
        }

        Schema::create('shop_weekly_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('week_year');
            $table->unsignedSmallInteger('week_number');
            $table->string('category');
            $table->unsignedTinyInteger('position');
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->timestamps();

            $table->unique(['week_year', 'week_number', 'category', 'position'], 'shop_weekly_unique');
            $table->index(['item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_weekly_offers');
    }
};
