<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->enum('policy_type', ['seasonal', 'demand', 'length_of_stay', 'special_event']);
            $table->enum('adjustment_type', ['percentage', 'fixed_amount']);
            $table->decimal('adjustment_value', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->json('conditions')->nullable(); // Conditions for policy application
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->timestamps();

            $table->index(['hostel_id', 'is_active']);
            $table->index(['policy_type', 'valid_from', 'valid_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_policies');
    }
};
