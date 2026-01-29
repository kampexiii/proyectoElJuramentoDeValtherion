<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('bonus_strength')->default(0);
            $table->unsignedInteger('bonus_magic')->default(0);
            $table->unsignedInteger('bonus_defense')->default(0);
            $table->unsignedInteger('bonus_speed')->default(0);
            $table->boolean('is_admin_fixed')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mounts');
    }
};
