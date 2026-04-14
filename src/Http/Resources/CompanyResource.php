<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'branches' => BranchResource::collection($this->whenLoaded('branches')),
            'departments' => DepartmentResource::collection($this->whenLoaded('departments')),
            'positions' => PositionResource::collection($this->whenLoaded('positions')),
        ];
    }
}
