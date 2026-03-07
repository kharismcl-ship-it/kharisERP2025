<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            // Custodian — the employee responsible for this asset
            $table->foreignId('custodian_employee_id')
                ->nullable()
                ->after('company_id')
                ->constrained('hr_employees')
                ->nullOnDelete();

            // Warranty
            $table->date('warranty_expiry_date')->nullable()->after('serial_number');
            $table->string('warranty_vendor')->nullable()->after('warranty_expiry_date');
            $table->string('warranty_reference')->nullable()->after('warranty_vendor');

            // Insurance
            $table->string('insurance_policy_number')->nullable()->after('warranty_reference');
            $table->string('insurance_provider')->nullable()->after('insurance_policy_number');
            $table->decimal('insurance_value', 15, 2)->nullable()->after('insurance_provider');
            $table->date('insurance_expiry_date')->nullable()->after('insurance_value');
        });

        // Store depreciation run history (each time depreciation is posted)
        Schema::create('fixed_asset_depreciation_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained()->cascadeOnDelete();
            $table->date('period_end_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('accumulated_before', 15, 2);
            $table->decimal('accumulated_after', 15, 2);
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('posted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Maintenance & service records
        Schema::create('fixed_asset_maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained()->cascadeOnDelete();
            $table->enum('maintenance_type', [
                'preventive', 'corrective', 'inspection', 'calibration', 'overhaul', 'other',
            ])->default('preventive');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('contractor')->nullable();
            $table->date('next_due_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Asset location/custodian transfer history
        Schema::create('fixed_asset_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained()->cascadeOnDelete();
            $table->string('from_location')->nullable();
            $table->string('to_location');
            $table->foreignId('from_custodian_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->foreignId('to_custodian_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->date('transfer_date');
            $table->foreignId('transferred_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_asset_transfers');
        Schema::dropIfExists('fixed_asset_maintenance_records');
        Schema::dropIfExists('fixed_asset_depreciation_runs');

        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->dropForeign(['custodian_employee_id']);
            $table->dropColumn([
                'custodian_employee_id',
                'warranty_expiry_date', 'warranty_vendor', 'warranty_reference',
                'insurance_policy_number', 'insurance_provider', 'insurance_value', 'insurance_expiry_date',
            ]);
        });
    }
};
