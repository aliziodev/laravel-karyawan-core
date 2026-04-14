<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PositionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'code' => $this->code,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'company' => new CompanyResource($this->whenLoaded('company')),
        ];
    }
}
