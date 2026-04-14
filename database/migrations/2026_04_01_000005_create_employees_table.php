<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $employeesTable = config('karyawan.table_names.employees', 'employees');
        $companiesTable = config('karyawan.table_names.companies', 'companies');
        $branchesTable = config('karyawan.table_names.branches', 'branches');
        $departmentsTable = config('karyawan.table_names.departments', 'departments');
        $positionsTable = config('karyawan.table_names.positions', 'positions');

        Schema::create($employeesTable, function (Blueprint $table) use (
            $companiesTable,
            $branchesTable,
            $departmentsTable,
            $positionsTable,
            $employeesTable,
        ) {
            $table->id();

            // --- Relasi Opsional ---
            // user_id: nullable + unique → satu user hanya boleh terhubung ke satu employee
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained($companiesTable)->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained($branchesTable)->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained($departmentsTable)->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained($positionsTable)->nullOnDelete();
            // self-referencing untuk atasan langsung
            $table->foreignId('manager_employee_id')->nullable()->constrained($employeesTable)->nullOnDelete();

            // --- Identitas Utama ---
            $table->string('employee_code', 50)->unique();
            $table->string('full_name');
            $table->string('nick_name', 100)->nullable();

            // --- Kontak ---
            $table->string('work_email')->nullable()->unique();
            $table->string('personal_email')->nullable();
            $table->string('phone', 30)->nullable();

            // --- Data Kependudukan (sensitif) ---
            $table->string('national_id_number', 30)->nullable(); // NIK KTP
            $table->string('family_card_number', 30)->nullable(); // Nomor KK
            $table->string('tax_number', 30)->nullable();          // NPWP

            // --- Data Pribadi ---
            $table->string('gender', 20)->nullable();
            $table->string('religion', 30)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('citizenship', 60)->nullable()->default('WNI');

            // --- Alamat ---
            $table->text('permanent_address')->nullable();  // Alamat KTP
            $table->text('current_address')->nullable();    // Alamat Domisili

            // --- Foto ---
            $table->string('photo_path')->nullable();
            $table->string('photo_disk', 50)->nullable();

            // --- Data Kepegawaian ---
            $table->date('join_date')->nullable();
            $table->date('permanent_date')->nullable();
            $table->date('exit_date')->nullable();
            $table->string('employment_type', 30)->nullable();
            $table->string('active_status', 30)->default('active');

            // --- Catatan Internal ---
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('karyawan.table_names.employees', 'employees'));
    }
};
