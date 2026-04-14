<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\Employee;

use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Enums\EmploymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

abstract class BaseExportEmployeesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'integer', 'exists:'.config('karyawan.table_names.companies', 'companies').',id'],
            'branch_id' => ['nullable', 'integer', 'exists:'.config('karyawan.table_names.branches', 'branches').',id'],
            'department_id' => ['nullable', 'integer', 'exists:'.config('karyawan.table_names.departments', 'departments').',id'],
            'position_id' => ['nullable', 'integer', 'exists:'.config('karyawan.table_names.positions', 'positions').',id'],
            'active_status' => ['nullable', new Enum(ActiveStatus::class)],
            'employment_type' => ['nullable', new Enum(EmploymentType::class)],
            'with_login' => ['nullable', 'boolean'],
            'without_login' => ['nullable', 'boolean'],
            'join_date_from' => ['nullable', 'date'],
            'join_date_to' => ['nullable', 'date', 'after_or_equal:join_date_from'],
            'exit_date_from' => ['nullable', 'date'],
            'exit_date_to' => ['nullable', 'date', 'after_or_equal:exit_date_from'],
            'created_at_from' => ['nullable', 'date'],
            'created_at_to' => ['nullable', 'date', 'after_or_equal:created_at_from'],
            'sort_by' => ['nullable', Rule::in(['employee_code', 'full_name', 'join_date', 'active_status', 'created_at'])],
            'sort_direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.integer' => __('karyawan::validation.export.integer'),
            'branch_id.integer' => __('karyawan::validation.export.integer'),
            'department_id.integer' => __('karyawan::validation.export.integer'),
            'position_id.integer' => __('karyawan::validation.export.integer'),

            'company_id.exists' => __('karyawan::validation.export.exists'),
            'branch_id.exists' => __('karyawan::validation.export.exists'),
            'department_id.exists' => __('karyawan::validation.export.exists'),
            'position_id.exists' => __('karyawan::validation.export.exists'),

            'active_status.enum' => __('karyawan::validation.export.enum'),
            'employment_type.enum' => __('karyawan::validation.export.enum'),

            'with_login.boolean' => __('karyawan::validation.export.boolean'),
            'without_login.boolean' => __('karyawan::validation.export.boolean'),

            'join_date_from.date' => __('karyawan::validation.export.date'),
            'join_date_to.date' => __('karyawan::validation.export.date'),
            'join_date_to.after_or_equal' => __('karyawan::validation.export.after_or_equal'),

            'exit_date_from.date' => __('karyawan::validation.export.date'),
            'exit_date_to.date' => __('karyawan::validation.export.date'),
            'exit_date_to.after_or_equal' => __('karyawan::validation.export.after_or_equal'),

            'created_at_from.date' => __('karyawan::validation.export.date'),
            'created_at_to.date' => __('karyawan::validation.export.date'),
            'created_at_to.after_or_equal' => __('karyawan::validation.export.after_or_equal'),

            'sort_by.in' => __('karyawan::validation.export.in'),
            'sort_direction.in' => __('karyawan::validation.export.in'),
        ];
    }

    public function attributes(): array
    {
        return [
            'search' => __('karyawan::validation.export.attributes.search'),
            'company_id' => __('karyawan::validation.export.attributes.company_id'),
            'branch_id' => __('karyawan::validation.export.attributes.branch_id'),
            'department_id' => __('karyawan::validation.export.attributes.department_id'),
            'position_id' => __('karyawan::validation.export.attributes.position_id'),
            'active_status' => __('karyawan::validation.export.attributes.active_status'),
            'employment_type' => __('karyawan::validation.export.attributes.employment_type'),
            'with_login' => __('karyawan::validation.export.attributes.with_login'),
            'without_login' => __('karyawan::validation.export.attributes.without_login'),
            'join_date_from' => __('karyawan::validation.export.attributes.join_date_from'),
            'join_date_to' => __('karyawan::validation.export.attributes.join_date_to'),
            'exit_date_from' => __('karyawan::validation.export.attributes.exit_date_from'),
            'exit_date_to' => __('karyawan::validation.export.attributes.exit_date_to'),
            'created_at_from' => __('karyawan::validation.export.attributes.created_at_from'),
            'created_at_to' => __('karyawan::validation.export.attributes.created_at_to'),
            'sort_by' => __('karyawan::validation.export.attributes.sort_by'),
            'sort_direction' => __('karyawan::validation.export.attributes.sort_direction'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        $validated = $this->validated();

        foreach (['company_id', 'branch_id', 'department_id', 'position_id'] as $key) {
            if (array_key_exists($key, $validated)) {
                $validated[$key] = (int) $validated[$key];
            }
        }

        foreach (['with_login', 'without_login'] as $key) {
            if (array_key_exists($key, $validated)) {
                $validated[$key] = filter_var($validated[$key], FILTER_VALIDATE_BOOL);
            }
        }

        return $validated;
    }
}
