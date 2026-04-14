<?php

namespace Aliziodev\LaravelKaryawanCore\Actions;

use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData;
use Aliziodev\LaravelKaryawanCore\Events\EmployeeUpdated;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Support\Facades\DB;

class UpdateEmployeeAction
{
    public function execute(Employee $employee, EmployeeData $data): Employee
    {
        $attributes = $data->toArray();

        // employee_code tidak boleh diubah melalui update biasa
        unset($attributes['employee_code']);

        $changed = [];

        $employee = DB::transaction(function () use ($employee, $attributes, &$changed): Employee {
            $employee->fill($attributes);
            $changed = $employee->getDirty();
            $employee->save();

            return $employee->fresh() ?? $employee;
        });

        // Hanya dispatch event jika ada perubahan nyata
        if (! empty($changed)) {
            event(new EmployeeUpdated($employee, $changed));
        }

        return $employee;
    }
}
