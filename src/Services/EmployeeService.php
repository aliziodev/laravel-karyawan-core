<?php

namespace Aliziodev\LaravelKaryawanCore\Services;

use Aliziodev\LaravelKaryawanCore\Actions\ChangeEmployeeStatusAction;
use Aliziodev\LaravelKaryawanCore\Actions\CreateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\Actions\DeleteEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\Actions\LinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Actions\StoreEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\Actions\UnlinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Actions\UpdateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData;
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeDocumentData;
use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;

/**
 * Facade-friendly service layer.
 * Mengkoordinasikan action-action agar bisa dipanggil dari satu titik.
 */
class EmployeeService
{
    public function __construct(
        private readonly CreateEmployeeAction $createAction,
        private readonly UpdateEmployeeAction $updateAction,
        private readonly LinkEmployeeUserAction $linkUserAction,
        private readonly UnlinkEmployeeUserAction $unlinkUserAction,
        private readonly ChangeEmployeeStatusAction $changeStatusAction,
        private readonly StoreEmployeeDocumentAction $storeDocumentAction,
        private readonly DeleteEmployeeDocumentAction $deleteDocumentAction,
    ) {}

    public function create(EmployeeData $data): Employee
    {
        return $this->createAction->execute($data);
    }

    public function update(Employee $employee, EmployeeData $data): Employee
    {
        return $this->updateAction->execute($employee, $data);
    }

    public function linkUser(Employee $employee, int $userId, ?int $createdBy = null): Employee
    {
        return $this->linkUserAction->execute($employee, $userId, $createdBy);
    }

    public function unlinkUser(Employee $employee, ?int $createdBy = null): Employee
    {
        return $this->unlinkUserAction->execute($employee, $createdBy);
    }

    public function changeStatus(
        Employee $employee,
        ActiveStatus $status,
        ?string $effectiveDate = null,
        ?string $notes = null,
        ?int $createdBy = null,
    ): Employee {
        return $this->changeStatusAction->execute($employee, $status, $effectiveDate, $notes, $createdBy);
    }

    public function storeDocument(Employee $employee, EmployeeDocumentData $data): EmployeeDocument
    {
        return $this->storeDocumentAction->execute($employee, $data);
    }

    public function deleteDocument(EmployeeDocument $document): void
    {
        $this->deleteDocumentAction->execute($document);
    }
}
