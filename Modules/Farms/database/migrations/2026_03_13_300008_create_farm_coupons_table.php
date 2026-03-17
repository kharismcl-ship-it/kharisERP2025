<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('code', 50);
            $table->enum('type', ['percentage', 'fixed'])->default('fixed');
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('uses_count')->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::table('farm_orders', function (Blueprint $table) {
            $table->string('coupon_code', 50)->nullable()->after('notes');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('coupon_code');
        });
    }

    public function down(): void
    {
        Schema::table('farm_orders', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'discount_amount']);
        });
        Schema::dropIfExists('farm_coupons');
    }
};
