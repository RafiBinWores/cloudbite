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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->enum('coupon_type', ['default', 'first_order'])->default('default');
            $table->string('title');
            $table->string('coupon_code')->unique();
            $table->integer('same_user_limit');
            $table->enum('discount_type', ['percent', 'amount'])->default('percent');
            $table->decimal('discount', 10, 2);
            $table->date('start_date');
            $table->date('expire_date');
            $table->integer('minimum_purchase')->nullable();
            $table->enum('status', ['active', 'disable'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
