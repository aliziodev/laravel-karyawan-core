<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class LinkEmployeeUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => __('karyawan::validation.common.required'),
            'user_id.integer' => __('karyawan::validation.common.integer'),
            'user_id.min' => __('karyawan::validation.common.min_numeric'),
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => __('karyawan::validation.attributes.user_id'),
        ];
    }
}
