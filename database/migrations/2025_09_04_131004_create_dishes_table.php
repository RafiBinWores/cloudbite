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
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cuisine_id')->constrained()->cascadeOnDelete();
            $table->string('title')->unique();
            $table->string('slug');
            $table->string('short_description');
            $table->longText('description');
            $table->decimal('price', 10, 2);
            $table->enum('discount_type', ['amount', 'percent'])->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('vat', 5, 2)->nullable();
            $table->string('thumbnail');
            $table->boolean('show_in_hero')->default(false);
            $table->string('hero_image')->nullable();
            $table->string('hero_discount_image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('sku')->nullable();
            $table->enum('track_stock', ['Yes', 'No'])->default('No');
            $table->unsignedInteger('daily_stock')->nullable();
            $table->time('available_from');
            $table->time('available_till');
            $table->enum('visibility', ['Yes', 'No'])->default('Yes');
            $table->json('tags')->nullable();
            $table->json('variations')->nullable();
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
