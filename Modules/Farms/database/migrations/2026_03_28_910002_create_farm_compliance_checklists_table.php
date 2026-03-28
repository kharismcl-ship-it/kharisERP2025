<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_compliance_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('farm_certification_id')->nullable()->constrained('farm_certifications')->nullOnDelete();
            $table->string('checklist_name');
            $table->enum('checklist_type', ['globalGAP', 'organic', 'food_safety', 'custom']);
            $table->json('items')->nullable(); // [{id, question, status: yes/no/na/pending, evidence, notes}]
            $table->decimal('completion_pct', 5, 2)->default(0);
            $table->string('conducted_by')->nullable();
            $table->date('audit_date')->nullable();
            $table->date('next_audit_date')->nullable();
            $table->enum('outcome', ['pass', 'fail', 'conditional_pass', 'pending'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('farm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_compliance_checklists');
    }
};