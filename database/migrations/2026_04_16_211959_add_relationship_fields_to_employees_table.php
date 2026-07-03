<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null')->after('name');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null')->after('position_id');
            $table->foreignId('employee_type_id')->nullable()->constrained()->onDelete('set null')->after('department_id');
            $table->string('photo')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['employee_type_id']);
            $table->dropColumn(['position_id', 'department_id', 'employee_type_id', 'photo']);
        });
    }
};
