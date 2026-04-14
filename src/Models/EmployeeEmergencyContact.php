<?php

namespace Aliziodev\LaravelKaryawanCore\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEmergencyContact extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'relationship',
        'phone',
        'address',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function getTable(): string
    {
        return config('karyawan.table_prefix', '').config('karyawan.table_names.employee_emergency_contacts', 'employee_emergency_contacts');
    }

    // --- Relations ---

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // --- Scopes ---

    public function scopePrimary(Builder $query): void
    {
        $query->where('is_primary', true);
    }
}
