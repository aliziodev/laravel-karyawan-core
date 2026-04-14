<?php

namespace Aliziodev\LaravelKaryawanCore\Actions;

use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Enums\HistoryType;
use Aliziodev\LaravelKaryawanCore\Events\EmployeeStatusChanged;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeHistory;
use Illuminate\Support\Facades\DB;

class ChangeEmployeeStatusAction
{
    public function execute(
        Employee $employee,
        ActiveStatus $newStatus,
        ?string $effectiveDate = null,
        ?string $notes = null,
        ?int $createdBy = null,
    ): Employee {
        $previousStatus = $employee->active_status;

        if ($previousStatus === $newStatus) {
            return $employee;
        }

        $employee = DB::transaction(function () use ($employee, $newStatus, $effectiveDate, $notes, $createdBy, $previousStatus): Employee {
            // Catat exit_date otomatis jika status adalah non-aktif permanen
            $exitDate = in_array($newStatus, [
                ActiveStatus::Resigned,
                ActiveStatus::Terminated,
                ActiveStatus::Retired,
            ]) ? ($effectiveDate ?? now()->toDateString()) : null;

            $employee->active_status = $newStatus;

            if ($exitDate !== null && $employee->exit_date === null) {
                $employee->exit_date = $exitDate;
            }

            $employee->save();

            EmployeeHistory::create([
                'employee_id' => $employee->id,
                'type' => HistoryType::StatusChange->value,
                'old_value' => ['active_status' => $previousStatus->value],
                'new_value' => ['active_status' => $newStatus->value],
                'effective_date' => $effectiveDate ?? now()->toDateString(),
                'notes' => $notes,
                'created_by' => $createdBy,
            ]);

            return $employee->fresh() ?? $employee;
        });

        event(new EmployeeStatusChanged($employee, $previousStatus, $newStatus));

        return $employee;
    }
}
