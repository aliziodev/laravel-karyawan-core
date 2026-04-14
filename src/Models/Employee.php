<?php

namespace Aliziodev\LaravelKaryawanCore\Models;

use Aliziodev\LaravelKaryawanCore\Database\Factories\EmployeeFactory;
use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Enums\EmploymentType;
use Aliziodev\LaravelKaryawanCore\Enums\Gender;
use Aliziodev\LaravelKaryawanCore\Enums\MaritalStatus;
use Aliziodev\LaravelKaryawanCore\Enums\Religion;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function newFactory(): EmployeeFactory
    {
        return EmployeeFactory::new();
    }

    protected $fillable = [
        'user_id',
        'company_id',
        'branch_id',
        'department_id',
        'position_id',
        'manager_employee_id',
        'employee_code',
        'full_name',
        'nick_name',
        'work_email',
        'personal_email',
        'phone',
        'national_id_number',
        'family_card_number',
        'tax_number',
        'gender',
        'religion',
        'marital_status',
        'birth_place',
        'birth_date',
        'citizenship',
        'permanent_address',
        'current_address',
        'photo_path',
        'photo_disk',
        'join_date',
        'permanent_date',
        'exit_date',
        'employment_type',
        'active_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
            'religion' => Religion::class,
            'marital_status' => MaritalStatus::class,
            'employment_type' => EmploymentType::class,
            'active_status' => ActiveStatus::class,
            'birth_date' => 'date',
            'join_date' => 'date',
            'permanent_date' => 'date',
            'exit_date' => 'date',
        ];
    }

    public function getTable(): string
    {
        return config('karyawan.table_prefix', '').config('karyawan.table_names.employees', 'employees');
    }

    // --- Relations ---

    public function user(): BelongsTo
    {
        $userModel = config('karyawan.user_model', User::class);

        return $this->belongsTo($userModel, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'manager_employee_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(self::class, 'manager_employee_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmployeeEmergencyContact::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(EmployeeHistory::class)->orderByDesc('created_at');
    }

    // --- Scopes ---

    public function scopeActive(Builder $query): void
    {
        $query->where('active_status', ActiveStatus::Active->value);
    }

    public function scopeInactive(Builder $query): void
    {
        $query->where('active_status', '!=', ActiveStatus::Active->value);
    }

    public function scopeWithLogin(Builder $query): void
    {
        $query->whereNotNull('user_id');
    }

    public function scopeWithoutLogin(Builder $query): void
    {
        $query->whereNull('user_id');
    }

    public function scopeByCompany(Builder $query, int $companyId): void
    {
        $query->where('company_id', $companyId);
    }

    public function scopeByBranch(Builder $query, int $branchId): void
    {
        $query->where('branch_id', $branchId);
    }

    public function scopeByDepartment(Builder $query, int $departmentId): void
    {
        $query->where('department_id', $departmentId);
    }

    public function scopeByPosition(Builder $query, int $positionId): void
    {
        $query->where('position_id', $positionId);
    }

    public function scopeSearch(Builder $query, string $keyword): void
    {
        $keyword = '%'.$keyword.'%';

        $query->where(function (Builder $q) use ($keyword) {
            $q->where('full_name', 'like', $keyword)
                ->orWhere('employee_code', 'like', $keyword)
                ->orWhere('work_email', 'like', $keyword)
                ->orWhere('nick_name', 'like', $keyword);
        });
    }

    // --- Helpers ---

    public function hasLogin(): bool
    {
        return $this->user_id !== null;
    }

    public function isActive(): bool
    {
        return $this->active_status === ActiveStatus::Active;
    }

    public function isWorking(): bool
    {
        return $this->active_status?->isWorking() ?? false;
    }
}
