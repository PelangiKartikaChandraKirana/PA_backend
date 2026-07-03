<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absence_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->string('document_type');
            $table->string('title');
            $table->string('file_path')->nullable();

            $table->date('start_date');
            $table->date('end_date');

            $table->string('status')->default('pending');
            $table->string('approved_by')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['employee_id']);
            $table->index(['document_type']);
            $table->index(['status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence_documents');
    }
};