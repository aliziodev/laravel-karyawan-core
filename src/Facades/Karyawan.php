<?php

namespace Aliziodev\LaravelKaryawanCore\Facades;

use Aliziodev\LaravelKaryawanCore\Services\EmployeeService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Aliziodev\LaravelKaryawanCore\Models\Employee create(\Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData $data)
 * @method static \Aliziodev\LaravelKaryawanCore\Models\Employee update(\Aliziodev\LaravelKaryawanCore\Models\Employee $employee, \Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData $data)
 * @method static \Aliziodev\LaravelKaryawanCore\Models\Employee linkUser(\Aliziodev\LaravelKaryawanCore\Models\Employee $employee, int $userId, ?int $createdBy = null)
 * @method static \Aliziodev\LaravelKaryawanCore\Models\Employee unlinkUser(\Aliziodev\LaravelKaryawanCore\Models\Employee $employee, ?int $createdBy = null)
 * @method static \Aliziodev\LaravelKaryawanCore\Models\Employee changeStatus(\Aliziodev\LaravelKaryawanCore\Models\Employee $employee, \Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus $status, ?string $effectiveDate = null, ?string $notes = null, ?int $createdBy = null)
 * @method static \Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument storeDocument(\Aliziodev\LaravelKaryawanCore\Models\Employee $employee, \Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeDocumentData $data)
 * @method static void deleteDocument(\Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument $document)
 *
 * @see EmployeeService
 */
class Karyawan extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EmployeeService::class;
    }
}
