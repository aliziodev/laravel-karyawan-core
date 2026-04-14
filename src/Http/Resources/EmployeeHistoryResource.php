<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'type' => $this->type?->value,
            'type_label' => $this->type?->label(),
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'effective_date' => $this->effective_date?->toDateString(),
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
