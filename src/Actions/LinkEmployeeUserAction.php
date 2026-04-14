<?php

namespace Aliziodev\LaravelKaryawanCore\Actions;

use Aliziodev\LaravelKaryawanCore\Enums\HistoryType;
use Aliziodev\LaravelKaryawanCore\Events\EmployeeLinkedToUser;
use Aliziodev\LaravelKaryawanCore\Exceptions\EmployeeUserLinkException;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeHistory;
use Illuminate\Support\Facades\DB;

class LinkEmployeeUserAction
{
    public function execute(Employee $employee, int $userId, ?int $createdBy = null): Employee
    {
        $employee = DB::transaction(function () use ($employee, $userId, $createdBy): Employee {
            // Lock baris employee untuk mencegah race condition
            $employee = Employee::lockForUpdate()->findOrFail($employee->id);

            if ($employee->user_id !== null) {
                throw EmployeeUserLinkException::employeeAlreadyHasUser($employee->id);
            }

            // Cek apakah user sudah terhubung ke employee lain (selain unique index DB)
            // Ini sebagai early check sebelum DB constraint menangkap
            $alreadyLinked = Employee::where('user_id', $userId)->exists();
            if ($alreadyLinked) {
                throw EmployeeUserLinkException::userAlreadyLinked($userId);
            }

            $employee->user_id = $userId;
            $employee->save();

            EmployeeHistory::create([
                'employee_id' => $employee->id,
                'type' => HistoryType::UserLinked->value,
                'old_value' => ['user_id' => null],
                'new_value' => ['user_id' => $userId],
                'notes' => 'Akun login dikaitkan.',
                'created_by' => $createdBy,
            ]);

            return $employee->fresh() ?? $employee;
        });

        event(new EmployeeLinkedToUser($employee, $userId));

        return $employee;
    }
}
