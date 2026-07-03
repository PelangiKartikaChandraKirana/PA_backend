<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absence_documents', function (Blueprint $table) {
            $table->string('rejected_by')->nullable()->after('approved_by');
            $table->text('decision_notes')->nullable()->after('rejected_by');
            $table->timestamp('decided_at')->nullable()->after('decision_notes');

            $table->index(['decided_at']);
        });
    }

    public function down(): void
    {
        Schema::table('absence_documents', function (Blueprint $table) {
            $table->dropIndex(['decided_at']);
            $table->dropColumn(['rejected_by', 'decision_notes', 'decided_at']);
        });
    }
};
