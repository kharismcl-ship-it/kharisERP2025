<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->enum('contract_type', [
                'blanket_order',
                'framework',
                'fixed_price',
                'rate_contract',
                'service_agreement',
            ]);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_value', 15, 2)->nullable();
            $table->decimal('committed_value', 15, 2)->default(0);
            $table->string('currency', 10)->default('GHS');
            $table->smallInteger('payment_terms')->nullable();
            $table->enum('status', ['draft', 'active', 'expired', 'terminated', 'suspended'])->default('draft');
            $table->boolean('auto_renewal')->default(false);
            $table->tinyInteger('renewal_notice_days')->default(30);
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('procurement_contract_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('procurement_contracts')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('description');
            $table->string('unit_of_measure')->nullable();
            $table->decimal('agreed_unit_price', 15, 4);
            $table->decimal('min_quantity', 15, 4)->nullable();
            $table->decimal('max_quantity', 15, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_contract_lines');
        Schema::dropIfExists('procurement_contracts');
    }
};