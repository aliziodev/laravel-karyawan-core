<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\EmergencyContact;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmergencyContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => __('karyawan::validation.common.required'),
            '*.string' => __('karyawan::validation.common.string'),
            '*.boolean' => __('karyawan::validation.common.boolean'),
            '*.max' => __('karyawan::validation.common.max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('karyawan::validation.attributes.name'),
            'relationship' => __('karyawan::validation.attributes.relationship'),
            'phone' => __('karyawan::validation.attributes.phone'),
            'address' => __('karyawan::validation.attributes.address'),
            'is_primary' => __('karyawan::validation.attributes.is_primary'),
        ];
    }
}
