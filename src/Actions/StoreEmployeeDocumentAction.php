<?php

namespace Aliziodev\LaravelKaryawanCore\Actions;

use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeDocumentData;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;
use Illuminate\Support\Facades\DB;

class StoreEmployeeDocumentAction
{
    /**
     * Simpan metadata dokumen ke database.
     * File harus sudah diupload sebelumnya (via EmployeeDocumentService::storeFile).
     */
    public function execute(Employee $employee, EmployeeDocumentData $data): EmployeeDocument
    {
        return DB::transaction(function () use ($employee, $data): EmployeeDocument {
            return $employee->documents()->create($data->toArray());
        });
    }
}
