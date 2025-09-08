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
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('title')->unique();
            $table->string('slug');
            $table->string('short_description');
            $table->longText('description');

            // Relations
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cuisine_id')->constrained()->cascadeOnDelete();

            // Pricing & stock
            $table->decimal('price', 10, 2);
            $table->json('tags')->nullable(); 

            $table->enum('discount_type', ['amount', 'percent'])->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('vat', 5, 2)->nullable();

            $table->string('sku')->nullable();
            $table->enum('track_stock', ['Yes', 'No'])->default('No');
            $table->unsignedInteger('daily_stock')->nullable();

            // Availability & visibility
            $table->time('available_from');
            $table->time('available_till');
            $table->enum('visibility', ['Yes', 'No'])->default('Yes');

            // Media
            $table->string('thumbnail');
            $table->json('gallery')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
