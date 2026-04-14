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
}
