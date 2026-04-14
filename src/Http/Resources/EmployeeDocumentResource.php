<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'type' => $this->type?->value,
            'type_label' => $this->type?->label(),
            'name' => $this->name,
            'document_number' => $this->document_number,
            'issued_at' => $this->issued_at?->toDateString(),
            'expired_at' => $this->expired_at?->toDateString(),
            'is_expired' => $this->isExpired(),
            'file_disk' => $this->file_disk,
            'file_name' => $this->file_name,
            'file_extension' => $this->file_extension,
            'file_mime_type' => $this->file_mime_type,
            'file_size' => $this->file_size,
            'file_size_formatted' => $this->file_size_formatted,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
