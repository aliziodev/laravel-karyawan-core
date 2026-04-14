<?php

namespace Aliziodev\LaravelKaryawanCore\Actions;

use Aliziodev\LaravelKaryawanCore\Enums\HistoryType;
use Aliziodev\LaravelKaryawanCore\Events\EmployeeUnlinkedFromUser;
use Aliziodev\LaravelKaryawanCore\Exceptions\EmployeeUserLinkException;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeHistory;
use Illuminate\Support\Facades\DB;

class UnlinkEmployeeUserAction
{
    public function execute(Employee $employee, ?int $createdBy = null): Employee
    {
        $employee = DB::transaction(function () use ($employee, $createdBy): Employee {
            $employee = Employee::lockForUpdate()->findOrFail($employee->id);

            if ($employee->user_id === null) {
                throw EmployeeUserLinkException::employeeHasNoUser($employee->id);
            }

            $previousUserId = $employee->user_id;

            $employee->user_id = null;
            $employee->save();

            EmployeeHistory::create([
                'employee_id' => $employee->id,
                'type' => HistoryType::UserUnlinked->value,
                'old_value' => ['user_id' => $previousUserId],
                'new_value' => ['user_id' => null],
                'notes' => 'Akun login dilepas.',
                'created_by' => $createdBy,
            ]);

            return $employee->fresh() ?? $employee;
        });

        event(new EmployeeUnlinkedFromUser($employee, $previousUserId ?? 0));

        return $employee;
    }
}
