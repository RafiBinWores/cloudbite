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
        Schema::create('dish_related', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')->constrained('dishes')->cascadeOnDelete();
            $table->foreignId('related_dish_id')->constrained('dishes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['dish_id', 'related_dish_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dish_related');
    }
};
