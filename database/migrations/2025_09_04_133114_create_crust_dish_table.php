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
        Schema::create('crust_dish', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')->constrained('dishes')->cascadeOnDelete();
            $table->foreignId('crust_id')->constrained('crusts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['dish_id', 'crust_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crust_dish');
    }
};
