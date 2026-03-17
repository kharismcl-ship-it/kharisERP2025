<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_shop_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained()->cascadeOnDelete();

            // Branding
            $table->string('shop_name')->default('Alpha Farms');
            $table->string('tagline')->nullable()->default('Fresh from the farm to your table');
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('primary_color')->default('#15803d');   // green-700
            $table->string('secondary_color')->default('#166534'); // green-800

            // Contact
            $table->string('phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();

            // Delivery
            $table->decimal('delivery_fee', 10, 2)->default(20.00);
            $table->decimal('free_delivery_above', 10, 2)->nullable();
            $table->json('delivery_days')->nullable(); // ["monday","wednesday","friday"]
            $table->time('order_cutoff_time')->nullable()->default('18:00:00');

            // Homepage
            $table->string('hero_heading')->nullable()->default('Farm Fresh Produce, Delivered to You');
            $table->string('hero_subheading')->nullable()->default('Order online, receive fresh from the farm');
            $table->string('hero_image_path')->nullable();
            $table->string('announcement_bar_text')->nullable();
            $table->boolean('announcement_bar_active')->default(false);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image_path')->nullable();

            // Social
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();

            // Footer
            $table->text('footer_about_text')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_shop_settings');
    }
};
