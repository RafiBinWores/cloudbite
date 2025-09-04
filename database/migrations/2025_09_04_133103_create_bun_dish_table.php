<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bun_dish', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')->constrained('dishes')->cascadeOnDelete();
            $table->foreignId('bun_id')->constrained('buns')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['dish_id', 'bun_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bun_dish');
    }
};
