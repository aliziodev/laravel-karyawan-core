<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $historiesTable = config('karyawan.table_names.employee_histories', 'employee_histories');
        $employeesTable = config('karyawan.table_names.employees', 'employees');

        Schema::create($historiesTable, function (Blueprint $table) use ($employeesTable) {
            $table->id();
            $table->foreignId('employee_id')->constrained($employeesTable)->cascadeOnDelete();

            $table->string('type', 60);           // HistoryType enum value
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->date('effective_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // user_id who made the change

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('karyawan.table_names.employee_histories', 'employee_histories'));
    }
};
