<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_shop_blog_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('title');
            $table->string('slug');
            $table->string('category')->default('blog'); // blog | recipe
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('cover_image_path')->nullable();
            $table->json('tags')->nullable();
            $table->json('ingredients')->nullable(); // for recipes
            $table->unsignedSmallInteger('reading_time_minutes')->default(2);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->unique(['company_id', 'slug'], 'uniq_blog_co_slug');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_shop_blog_posts');
    }
};
