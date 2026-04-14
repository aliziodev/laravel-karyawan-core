<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $companiesTable = config('karyawan.table_names.companies', 'companies');
        $positionsTable = config('karyawan.table_names.positions', 'positions');

        Schema::create($positionsTable, function (Blueprint $table) use ($companiesTable) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained($companiesTable)->nullOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('karyawan.table_names.positions', 'positions'));
    }
};
