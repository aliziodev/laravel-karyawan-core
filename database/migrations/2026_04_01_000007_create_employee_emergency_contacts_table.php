<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $contactsTable = config('karyawan.table_names.employee_emergency_contacts', 'employee_emergency_contacts');
        $employeesTable = config('karyawan.table_names.employees', 'employees');

        Schema::create($contactsTable, function (Blueprint $table) use ($employeesTable) {
            $table->id();
            $table->foreignId('employee_id')->constrained($employeesTable)->cascadeOnDelete();

            $table->string('name');
            $table->string('relationship', 100);
            $table->string('phone', 30);
            $table->text('address')->nullable();
            $table->boolean('is_primary')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('karyawan.table_names.employee_emergency_contacts', 'employee_emergency_contacts'));
    }
};
