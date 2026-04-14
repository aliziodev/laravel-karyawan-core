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

    public function messages(): array
    {
        return [
            '*.required' => __('karyawan::validation.common.required'),
            '*.string' => __('karyawan::validation.common.string'),
            '*.integer' => __('karyawan::validation.common.integer'),
            '*.email' => __('karyawan::validation.common.email'),
            '*.date' => __('karyawan::validation.common.date'),
            '*.max' => __('karyawan::validation.common.max'),
            '*.exists' => __('karyawan::validation.common.exists'),
            '*.unique' => __('karyawan::validation.common.unique'),
            '*.after_or_equal' => __('karyawan::validation.common.after_or_equal'),
            '*.enum' => __('karyawan::validation.common.enum'),
            'manager_employee_id.not_in' => __('karyawan::validation.employee.manager_self'),
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => __('karyawan::validation.attributes.user_id'),
            'company_id' => __('karyawan::validation.attributes.company_id'),
            'branch_id' => __('karyawan::validation.attributes.branch_id'),
            'department_id' => __('karyawan::validation.attributes.department_id'),
            'position_id' => __('karyawan::validation.attributes.position_id'),
            'manager_employee_id' => __('karyawan::validation.attributes.manager_employee_id'),
            'full_name' => __('karyawan::validation.attributes.full_name'),
            'nick_name' => __('karyawan::validation.attributes.nick_name'),
            'work_email' => __('karyawan::validation.attributes.work_email'),
            'personal_email' => __('karyawan::validation.attributes.personal_email'),
            'phone' => __('karyawan::validation.attributes.phone'),
            'national_id_number' => __('karyawan::validation.attributes.national_id_number'),
            'family_card_number' => __('karyawan::validation.attributes.family_card_number'),
            'tax_number' => __('karyawan::validation.attributes.tax_number'),
            'gender' => __('karyawan::validation.attributes.gender'),
            'religion' => __('karyawan::validation.attributes.religion'),
            'marital_status' => __('karyawan::validation.attributes.marital_status'),
            'birth_place' => __('karyawan::validation.attributes.birth_place'),
            'birth_date' => __('karyawan::validation.attributes.birth_date'),
            'citizenship' => __('karyawan::validation.attributes.citizenship'),
            'permanent_address' => __('karyawan::validation.attributes.permanent_address'),
            'current_address' => __('karyawan::validation.attributes.current_address'),
            'join_date' => __('karyawan::validation.attributes.join_date'),
            'permanent_date' => __('karyawan::validation.attributes.permanent_date'),
            'exit_date' => __('karyawan::validation.attributes.exit_date'),
            'employment_type' => __('karyawan::validation.attributes.employment_type'),
            'active_status' => __('karyawan::validation.attributes.active_status'),
            'notes' => __('karyawan::validation.attributes.notes'),
        ];
    }
}
