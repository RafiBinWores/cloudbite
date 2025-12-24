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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_slider')->default(false);
            $table->dateTime('start_at')->nullable(); 
            $table->dateTime('end_at')->nullable(); 
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
            $table->enum('status', ['active', 'disable'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
