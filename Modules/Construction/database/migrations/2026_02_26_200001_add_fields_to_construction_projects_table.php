<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_projects', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('location')->nullable()->after('description');
            $table->string('client_name')->nullable()->after('location');
            $table->string('client_contact')->nullable()->after('client_name');
            $table->string('project_manager')->nullable()->after('client_contact');
            $table->date('start_date')->nullable()->after('project_manager');
            $table->date('expected_end_date')->nullable()->after('start_date');
            $table->date('actual_end_date')->nullable()->after('expected_end_date');
            $table->decimal('contract_value', 18, 2)->default(0)->after('actual_end_date');
            $table->decimal('budget', 18, 2)->default(0)->after('contract_value');
            $table->decimal('total_spent', 18, 2)->default(0)->after('budget');
            $table->string('status')->default('planning')->after('total_spent'); // planning, active, on_hold, completed, cancelled
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('construction_projects', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'location', 'client_name', 'client_contact',
                'project_manager', 'start_date', 'expected_end_date', 'actual_end_date',
                'contract_value', 'budget', 'total_spent', 'status', 'notes',
            ]);
        });
    }
};
