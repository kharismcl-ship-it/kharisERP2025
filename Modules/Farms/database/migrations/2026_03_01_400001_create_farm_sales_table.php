<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->nullOnDelete();
            $table->foreignId('livestock_batch_id')->nullable()->constrained('livestock_batches')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('sale_date');
            $table->string('product_name');
            $table->string('product_type', 50)->default('crop'); // crop, livestock, processed
            $table->decimal('quantity', 14, 3);
            $table->string('unit', 50)->nullable();
            $table->decimal('unit_price', 14, 4);
            $table->decimal('total_amount', 18, 2);
            $table->string('buyer_name')->nullable();
            $table->string('buyer_contact', 255)->nullable();
            $table->string('payment_status', 30)->default('pending'); // pending, partial, paid
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_sales');
    }
};
