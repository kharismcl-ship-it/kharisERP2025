<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_cheque_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->integer('series_from');
            $table->integer('series_to');
            $table->integer('current_leaf');
            $table->boolean('is_exhausted')->default(false);
            $table->timestamps();
        });

        Schema::create('fin_cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('cheque_book_id')->nullable()->constrained('fin_cheque_books')->nullOnDelete();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->string('cheque_number');
            $table->string('payee_name');
            $table->decimal('amount', 15, 2);
            $table->date('cheque_date');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('status')->default('issued'); // issued/presented/cleared/returned/void
            $table->date('cleared_date')->nullable();
            $table->string('return_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_cheques');
        Schema::dropIfExists('fin_cheque_books');
    }
};