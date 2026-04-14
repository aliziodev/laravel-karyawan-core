<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\Employee;

use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Enums\EmploymentType;
use Aliziodev\LaravelKaryawanCore\Enums\Gender;
use Aliziodev\LaravelKaryawanCore\Enums\MaritalStatus;
use Aliziodev\LaravelKaryawanCore\Enums\Religion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeesTable = config('karyawan.table_names.employees', 'employees');
        $employeeId = $this->route('employee')?->id ?? $this->route('employee');

        return [
            'user_id' => ['nullable', 'integer', Rule::unique($employeesTable, 'user_id')->ignore($employeeId)],
            'company_id' => ['nullable', 'integer', 'exists:'.config('karyawan.table_names.companies', 'companies').',id'],
            'branch_id' => ['nullable', 'integer', 'exists:'.config('karyawan.table_names.branches', 'branches').',id'],
            'department_id' => ['nullable', 'integer', 'exists:'.config('karyawan.table_names.departments', 'departments').',id'],
            'position_id' => ['nullable', 'integer', 'exists:'.config('karyawan.table_names.positions', 'positions').',id'],
            'manager_employee_id' => ['nullable', 'integer', 'exists:'.$employeesTable.',id', Rule::notIn([$employeeId])],

            'full_name' => ['required', 'string', 'max:255'],
            'nick_name' => ['nullable', 'string', 'max:100'],
            'work_email' => ['nullable', 'email', 'max:255', Rule::unique($employeesTable, 'work_email')->ignore($employeeId)],
            'personal_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],

            'national_id_number' => ['nullable', 'string', 'max:30'],
            'family_card_number' => ['nullable', 'string', 'max:30'],
            'tax_number' => ['nullable', 'string', 'max:30'],

            'gender' => ['nullable', new Enum(Gender::class)],
            'religion' => ['nullable', new Enum(Religion::class)],
            'marital_status' => ['nullable', new Enum(MaritalStatus::class)],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'citizenship' => ['nullable', 'string', 'max:60'],

            'permanent_address' => ['nullable', 'string'],
            'current_address' => ['nullable', 'string'],

            'join_date' => ['nullable', 'date'],
            'permanent_date' => ['nullable', 'date', 'after_or_equal:join_date'],
            'exit_date' => ['nullable', 'date'],
            'employment_type' => ['nullable', new Enum(EmploymentType::class)],
            'active_status' => ['nullable', new Enum(ActiveStatus::class)],

            'notes' => ['nullable', 'string'],
        ];
    }
}
