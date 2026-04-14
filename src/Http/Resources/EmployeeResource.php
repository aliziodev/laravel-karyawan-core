<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_code' => $this->employee_code,
            'full_name' => $this->full_name,
            'nick_name' => $this->nick_name,

            // Kontak
            'work_email' => $this->work_email,
            'personal_email' => $this->when($request->user()?->can('view', $this->resource), $this->personal_email),
            'phone' => $this->phone,

            // Identitas (hanya tampil jika punya izin)
            'national_id_number' => $this->when($request->user()?->can('viewSensitive', $this->resource), $this->national_id_number),
            'family_card_number' => $this->when($request->user()?->can('viewSensitive', $this->resource), $this->family_card_number),
            'tax_number' => $this->when($request->user()?->can('viewSensitive', $this->resource), $this->tax_number),

            // Pribadi
            'gender' => $this->gender?->value,
            'gender_label' => $this->gender?->label(),
            'religion' => $this->religion?->value,
            'religion_label' => $this->religion?->label(),
            'marital_status' => $this->marital_status?->value,
            'marital_status_label' => $this->marital_status?->label(),
            'birth_place' => $this->birth_place,
            'birth_date' => $this->birth_date?->toDateString(),
            'citizenship' => $this->citizenship,

            // Alamat
            'permanent_address' => $this->permanent_address,
            'current_address' => $this->current_address,

            // Foto
            'photo_path' => $this->photo_path,
            'photo_disk' => $this->photo_disk,

            // Kepegawaian
            'join_date' => $this->join_date?->toDateString(),
            'permanent_date' => $this->permanent_date?->toDateString(),
            'exit_date' => $this->exit_date?->toDateString(),
            'employment_type' => $this->employment_type?->value,
            'employment_type_label' => $this->employment_type?->label(),
            'active_status' => $this->active_status?->value,
            'active_status_label' => $this->active_status?->label(),

            // Relasi organisasi
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'department_id' => $this->department_id,
            'position_id' => $this->position_id,
            'manager_employee_id' => $this->manager_employee_id,

            'company' => new CompanyResource($this->whenLoaded('company')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'position' => new PositionResource($this->whenLoaded('position')),
            'manager' => new self($this->whenLoaded('manager')),

            'documents' => EmployeeDocumentResource::collection($this->whenLoaded('documents')),
            'emergency_contacts' => EmployeeEmergencyContactResource::collection($this->whenLoaded('emergencyContacts')),
            'histories' => EmployeeHistoryResource::collection($this->whenLoaded('histories')),

            // Helpers
            'has_login' => $this->hasLogin(),
            'is_active' => $this->isActive(),

            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
