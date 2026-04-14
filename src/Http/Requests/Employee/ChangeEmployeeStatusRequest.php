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
}
