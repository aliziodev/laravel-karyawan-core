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
}
