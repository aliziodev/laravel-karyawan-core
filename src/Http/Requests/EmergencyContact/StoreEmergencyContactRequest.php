<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\EmergencyContact;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmergencyContactRequest extends FormRequest
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
}
