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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
             $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();

            $table->string('order_code')->unique(); // e.g. ORD-20251004-XXXX

            // Money buckets (copy from cart at checkout time)
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('shipping_total', 12, 2)->default(0); 
            $table->decimal('grand_total', 12, 2)->default(0);

            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_value', 12, 2)->nullable();

            // Contact & shipping
            $table->string('contact_name');
            $table->string('phone', 50);
            $table->string('email')->nullable();
            
            $table->json('shipping_address')->nullable();
            $table->text('customer_note')->nullable();

            // Payment & status
            $table->enum('payment_method', ['cod','sslcommerz'])->default('cod');
            $table->enum('payment_status', ['unpaid','paid','refunded'])->default('unpaid');
            $table->enum('order_status', ['pending','processing','confirmed','preparing','out_for_delivery', 'delivered', 'cancelled', 'returned', 'failed_to_deliver'])->default('pending');
            $table->unsignedSmallInteger('cooking_time_min')->nullable();

            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
