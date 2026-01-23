<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_mount_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('slot')->default('mount_armor');
            $table->foreignId('item_id')->nullable()->constrained('items')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['character_id', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_mount_equipment');
    }
};
