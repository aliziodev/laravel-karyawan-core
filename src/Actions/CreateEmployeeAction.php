<?php

namespace Aliziodev\LaravelKaryawanCore\Actions;

use Aliziodev\LaravelKaryawanCore\Contracts\EmployeeCodeGeneratorContract;
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData;
use Aliziodev\LaravelKaryawanCore\Events\EmployeeCreated;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Support\Facades\DB;

class CreateEmployeeAction
{
    public function __construct(
        private readonly EmployeeCodeGeneratorContract $codeGenerator,
    ) {}

    public function execute(EmployeeData $data): Employee
    {
        // Jalankan dalam transaction karena:
        // 1. generate employee_code harus atomik
        // 2. insert employee harus atomik
        $employee = DB::transaction(function () use ($data): Employee {
            $attributes = $data->toArray();

            if (empty($attributes['employee_code']) && config('karyawan.employee_code.auto_generate', true)) {
                $attributes['employee_code'] = $this->codeGenerator->generate();
            }

            return Employee::create($attributes);
        });

        // Event dispatch di luar transaction agar listener tidak terikat commit
        event(new EmployeeCreated($employee));

        return $employee;
    }
}
