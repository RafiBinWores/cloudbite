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
        Schema::create('meal_plan_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('booking_code', 30)->unique();

            // Plan info
            $table->string('plan_type', 20);
            $table->date('start_date');
            $table->json('meal_prefs');
            $table->json('days');

            // Totals
            $table->decimal('plan_subtotal', 10, 2);
            $table->decimal('shipping_total', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);

            // Payment split
            $table->enum('payment_option', ['full', 'half']);
            $table->decimal('pay_now', 10, 2);
            $table->decimal('due_amount', 10, 2);

            // Payment / status
            $table->string('payment_method', 50);
            $table->string('payment_status', 30)->default('unpaid');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');

            // Contact & shipping details
            $table->string('contact_name', 191);
            $table->string('phone', 50);
            $table->string('email', 191)->nullable();

            $table->json('shipping_address');
            $table->text('customer_note')->nullable();

            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plan_bookings');
    }
};
