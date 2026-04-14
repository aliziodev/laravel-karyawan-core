<?php

namespace Aliziodev\LaravelKaryawanCore\DataTransferObjects;

use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Enums\EmploymentType;
use Aliziodev\LaravelKaryawanCore\Enums\Gender;
use Aliziodev\LaravelKaryawanCore\Enums\MaritalStatus;
use Aliziodev\LaravelKaryawanCore\Enums\Religion;
use Illuminate\Foundation\Http\FormRequest;

readonly class EmployeeData
{
    public function __construct(
        public string $full_name,
        public ?string $employee_code = null,
        public ?int $user_id = null,
        public ?int $company_id = null,
        public ?int $branch_id = null,
        public ?int $department_id = null,
        public ?int $position_id = null,
        public ?int $manager_employee_id = null,
        public ?string $nick_name = null,
        public ?string $work_email = null,
        public ?string $personal_email = null,
        public ?string $phone = null,
        public ?string $national_id_number = null,
        public ?string $family_card_number = null,
        public ?string $tax_number = null,
        public ?Gender $gender = null,
        public ?Religion $religion = null,
        public ?MaritalStatus $marital_status = null,
        public ?string $birth_place = null,
        public ?string $birth_date = null,
        public ?string $citizenship = 'WNI',
        public ?string $permanent_address = null,
        public ?string $current_address = null,
        public ?string $photo_path = null,
        public ?string $photo_disk = null,
        public ?string $join_date = null,
        public ?string $permanent_date = null,
        public ?string $exit_date = null,
        public ?EmploymentType $employment_type = null,
        public ActiveStatus $active_status = ActiveStatus::Active,
        public ?string $notes = null,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            full_name: $request->string('full_name')->toString(),
            employee_code: $request->input('employee_code'),
            user_id: $request->input('user_id'),
            company_id: $request->input('company_id'),
            branch_id: $request->input('branch_id'),
            department_id: $request->input('department_id'),
            position_id: $request->input('position_id'),
            manager_employee_id: $request->input('manager_employee_id'),
            nick_name: $request->input('nick_name'),
            work_email: $request->input('work_email'),
            personal_email: $request->input('personal_email'),
            phone: $request->input('phone'),
            national_id_number: $request->input('national_id_number'),
            family_card_number: $request->input('family_card_number'),
            tax_number: $request->input('tax_number'),
            gender: $request->enum('gender', Gender::class),
            religion: $request->enum('religion', Religion::class),
            marital_status: $request->enum('marital_status', MaritalStatus::class),
            birth_place: $request->input('birth_place'),
            birth_date: $request->input('birth_date'),
            citizenship: $request->input('citizenship', 'WNI'),
            permanent_address: $request->input('permanent_address'),
            current_address: $request->input('current_address'),
            join_date: $request->input('join_date'),
            permanent_date: $request->input('permanent_date'),
            exit_date: $request->input('exit_date'),
            employment_type: $request->enum('employment_type', EmploymentType::class),
            active_status: $request->enum('active_status', ActiveStatus::class) ?? ActiveStatus::Active,
            notes: $request->input('notes'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            full_name: $data['full_name'],
            employee_code: $data['employee_code'] ?? null,
            user_id: $data['user_id'] ?? null,
            company_id: $data['company_id'] ?? null,
            branch_id: $data['branch_id'] ?? null,
            department_id: $data['department_id'] ?? null,
            position_id: $data['position_id'] ?? null,
            manager_employee_id: $data['manager_employee_id'] ?? null,
            nick_name: $data['nick_name'] ?? null,
            work_email: $data['work_email'] ?? null,
            personal_email: $data['personal_email'] ?? null,
            phone: $data['phone'] ?? null,
            national_id_number: $data['national_id_number'] ?? null,
            family_card_number: $data['family_card_number'] ?? null,
            tax_number: $data['tax_number'] ?? null,
            gender: isset($data['gender']) ? Gender::from($data['gender']) : null,
            religion: isset($data['religion']) ? Religion::from($data['religion']) : null,
            marital_status: isset($data['marital_status']) ? MaritalStatus::from($data['marital_status']) : null,
            birth_place: $data['birth_place'] ?? null,
            birth_date: $data['birth_date'] ?? null,
            citizenship: $data['citizenship'] ?? 'WNI',
            permanent_address: $data['permanent_address'] ?? null,
            current_address: $data['current_address'] ?? null,
            photo_path: $data['photo_path'] ?? null,
            photo_disk: $data['photo_disk'] ?? null,
            join_date: $data['join_date'] ?? null,
            permanent_date: $data['permanent_date'] ?? null,
            exit_date: $data['exit_date'] ?? null,
            employment_type: isset($data['employment_type']) ? EmploymentType::from($data['employment_type']) : null,
            active_status: isset($data['active_status']) ? ActiveStatus::from($data['active_status']) : ActiveStatus::Active,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'employee_code' => $this->employee_code,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'department_id' => $this->department_id,
            'position_id' => $this->position_id,
            'manager_employee_id' => $this->manager_employee_id,
            'full_name' => $this->full_name,
            'nick_name' => $this->nick_name,
            'work_email' => $this->work_email,
            'personal_email' => $this->personal_email,
            'phone' => $this->phone,
            'national_id_number' => $this->national_id_number,
            'family_card_number' => $this->family_card_number,
            'tax_number' => $this->tax_number,
            'gender' => $this->gender?->value,
            'religion' => $this->religion?->value,
            'marital_status' => $this->marital_status?->value,
            'birth_place' => $this->birth_place,
            'birth_date' => $this->birth_date,
            'citizenship' => $this->citizenship,
            'permanent_address' => $this->permanent_address,
            'current_address' => $this->current_address,
            'photo_path' => $this->photo_path,
            'photo_disk' => $this->photo_disk,
            'join_date' => $this->join_date,
            'permanent_date' => $this->permanent_date,
            'exit_date' => $this->exit_date,
            'employment_type' => $this->employment_type?->value,
            'active_status' => $this->active_status->value,
            'notes' => $this->notes,
        ], fn ($value) => $value !== null);
    }
}
