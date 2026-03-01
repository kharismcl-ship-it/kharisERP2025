<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'none'])->default('straight_line');
            $table->decimal('useful_life_years', 5, 1)->default(5);
            $table->decimal('residual_rate', 5, 2)->default(0); // % of cost retained at end
            $table->foreignId('asset_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('depreciation_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('accumulated_depreciation_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('acquisition_date');
            $table->decimal('cost', 15, 2);
            $table->decimal('residual_value', 15, 2)->default(0);
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->date('depreciation_start_date');
            $table->date('disposal_date')->nullable();
            $table->decimal('disposal_amount', 15, 2)->nullable();
            $table->enum('status', ['active', 'disposed', 'written_off'])->default('active');
            $table->string('location')->nullable();
            $table->string('serial_number')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
        Schema::dropIfExists('asset_categories');
    }
};
