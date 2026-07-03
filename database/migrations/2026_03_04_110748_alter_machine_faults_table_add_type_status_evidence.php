<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('machine_faults', function (Blueprint $table) {
            if (!Schema::hasColumn('machine_faults', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('machine_faults', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('machine_faults', 'machine_fault_type_id')) {
                $table->foreignId('machine_fault_type_id')->nullable()
                    ->constrained('machine_fault_types')->nullOnDelete();
            }

            if (!Schema::hasColumn('machine_faults', 'machine_fault_status_id')) {
                $table->foreignId('machine_fault_status_id')->nullable()
                    ->constrained('machine_fault_statuses')->nullOnDelete();
            }

            if (!Schema::hasColumn('machine_faults', 'incident_date')) {
                $table->date('incident_date')->nullable();
            }

            if (!Schema::hasColumn('machine_faults', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('machine_faults', 'evidence_path')) {
                $table->string('evidence_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('machine_faults', function (Blueprint $table) {
            // optional rollback (boleh kamu kosongkan)
        });
    }
};