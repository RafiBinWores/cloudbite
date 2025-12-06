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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            // 'single_order' or 'meal_plan_booking'
            $table->string('key')->unique();

            // Content
            $table->string('logo_path')->nullable();
            $table->string('main_title')->nullable();
            $table->string('header_title')->nullable();
            $table->longText('body')->nullable();
            $table->string('button_text')->nullable();
            $table->text('footer_section')->nullable();
            $table->string('copyright')->nullable();

            // Policy flags
            $table->boolean('show_privacy_policy')->default(true);
            $table->boolean('show_refund_policy')->default(true);
            $table->boolean('show_cancellation_policy')->default(true);
            $table->boolean('show_contact_us')->default(true);

            // Social flags (matching your company_info fields)
            $table->boolean('show_facebook')->default(true);
            $table->boolean('show_instagram')->default(true);
            $table->boolean('show_twitter')->default(true);
            $table->boolean('show_tiktok')->default(true);
            $table->boolean('show_youtube')->default(true);
            $table->boolean('show_whatsapp')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
