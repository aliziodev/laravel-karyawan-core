<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $companiesTable = config('karyawan.table_names.companies', 'companies');
        $branchesTable = config('karyawan.table_names.branches', 'branches');

        Schema::create($branchesTable, function (Blueprint $table) use ($companiesTable) {
            $table->id();
            $table->foreignId('company_id')->constrained($companiesTable)->cascadeOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('karyawan.table_names.branches', 'branches'));
    }
};
