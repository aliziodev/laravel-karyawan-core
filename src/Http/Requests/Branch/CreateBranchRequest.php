<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $table = config('karyawan.table_names.branches', 'branches');

        return [
            'company_id' => ['required', 'integer', 'exists:'.config('karyawan.table_names.companies', 'companies').',id'],
            'code' => ['required', 'string', 'max:50', Rule::unique($table, 'code')],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
