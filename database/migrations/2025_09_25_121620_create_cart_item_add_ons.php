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
        Schema::create('cart_item_add_ons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_item_id')->constrained('cart_items')->cascadeOnDelete();
            $table->foreignId('add_on_id')->constrained('add_ons')->cascadeOnDelete();
            $table->unsignedInteger('qty')->default(1); // if you allow multi-qty of same add-on; keep 1 if not
            $table->decimal('unit_extra', 12, 2)->default(0); // snapshot from add_on_dish.extra_price
            $table->timestamps();

            $table->unique(['cart_item_id', 'add_on_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item_add_ons');
    }
};
