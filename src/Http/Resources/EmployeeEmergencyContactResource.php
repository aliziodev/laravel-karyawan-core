<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeEmergencyContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'name' => $this->name,
            'relationship' => $this->relationship,
            'phone' => $this->phone,
            'address' => $this->address,
            'is_primary' => $this->is_primary,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
