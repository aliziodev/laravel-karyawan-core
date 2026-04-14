<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\Employee;

use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ChangeEmployeeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'active_status' => ['required', new Enum(ActiveStatus::class)],
            'effective_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'active_status.required' => __('karyawan::validation.common.required'),
            'active_status.enum' => __('karyawan::validation.common.enum'),
            'effective_date.date' => __('karyawan::validation.common.date'),
            'notes.string' => __('karyawan::validation.common.string'),
            'notes.max' => __('karyawan::validation.common.max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'active_status' => __('karyawan::validation.attributes.active_status'),
            'effective_date' => __('karyawan::validation.attributes.effective_date'),
            'notes' => __('karyawan::validation.attributes.notes'),
        ];
    }
}
