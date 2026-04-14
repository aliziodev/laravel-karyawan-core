<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $table = config('karyawan.table_names.companies', 'companies');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique($table, 'code')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
