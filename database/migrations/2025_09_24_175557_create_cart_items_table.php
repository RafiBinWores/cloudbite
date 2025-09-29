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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dish_id')->constrained('dishes')->cascadeOnDelete();

            $table->unsignedInteger('qty');
            $table->foreignId('crust_id')->nullable()->constrained('crusts')->nullOnDelete();
            $table->foreignId('bun_id')->nullable()->constrained('buns')->nullOnDelete();
            $table->json('addon_ids')->nullable();
            $table->decimal('unit_price', 12, 2); // base + crust + bun + addons (per unit)
            $table->decimal('line_total', 12, 2); // unit_price * qty
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['cart_id', 'dish_id', 'crust_id', 'bun_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
