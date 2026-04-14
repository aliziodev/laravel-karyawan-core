<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $table = config('karyawan.table_names.companies', 'companies');
        $companyId = $this->route('company')?->id ?? $this->route('company');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique($table, 'code')->ignore($companyId)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => __('karyawan::validation.common.required'),
            '*.string' => __('karyawan::validation.common.string'),
            '*.email' => __('karyawan::validation.common.email'),
            '*.boolean' => __('karyawan::validation.common.boolean'),
            '*.max' => __('karyawan::validation.common.max'),
            '*.unique' => __('karyawan::validation.common.unique'),
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => __('karyawan::validation.attributes.code'),
            'name' => __('karyawan::validation.attributes.name'),
            'email' => __('karyawan::validation.attributes.email'),
            'phone' => __('karyawan::validation.attributes.phone'),
            'address' => __('karyawan::validation.attributes.address'),
            'is_active' => __('karyawan::validation.attributes.is_active'),
        ];
    }
}
