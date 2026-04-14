<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\Position;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $table = config('karyawan.table_names.positions', 'positions');
        $positionId = $this->route('position')?->id ?? $this->route('position');

        return [
            'company_id' => ['nullable', 'integer', 'exists:'.config('karyawan.table_names.companies', 'companies').',id'],
            'code' => ['required', 'string', 'max:50', Rule::unique($table, 'code')->ignore($positionId)],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => __('karyawan::validation.common.required'),
            '*.string' => __('karyawan::validation.common.string'),
            '*.integer' => __('karyawan::validation.common.integer'),
            '*.boolean' => __('karyawan::validation.common.boolean'),
            '*.max' => __('karyawan::validation.common.max'),
            '*.exists' => __('karyawan::validation.common.exists'),
            '*.unique' => __('karyawan::validation.common.unique'),
        ];
    }

    public function attributes(): array
    {
        return [
            'company_id' => __('karyawan::validation.attributes.company_id'),
            'code' => __('karyawan::validation.attributes.code'),
            'name' => __('karyawan::validation.attributes.name'),
            'is_active' => __('karyawan::validation.attributes.is_active'),
        ];
    }
}
