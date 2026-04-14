<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $documentsTable = config('karyawan.table_names.employee_documents', 'employee_documents');
        $employeesTable = config('karyawan.table_names.employees', 'employees');

        Schema::create($documentsTable, function (Blueprint $table) use ($employeesTable) {
            $table->id();
            $table->foreignId('employee_id')->constrained($employeesTable)->cascadeOnDelete();

            // --- Metadata Dokumen ---
            $table->string('type', 50);             // DocumentType enum value
            $table->string('name');
            $table->string('document_number', 100)->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expired_at')->nullable();

            // --- Info File ---
            $table->string('file_disk', 50);
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_extension', 20)->nullable();
            $table->string('file_mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();  // bytes
            $table->string('checksum', 64)->nullable();            // sha256

            // --- Tambahan ---
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('karyawan.table_names.employee_documents', 'employee_documents'));
    }
};
