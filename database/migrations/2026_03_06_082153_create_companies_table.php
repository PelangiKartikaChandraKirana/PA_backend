<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('kode_opd',20);
            $table->string('short_name',100)->nullable();
            $table->string('type',50)->nullable();
            $table->integer('parent_id')->nullable();
            $table->text('address')->nullable();
            $table->string('phone',50)->nullable();
            $table->string('email',100)->nullable();
            $table->boolean('is_active')->default(1);

            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};