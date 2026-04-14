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
}
