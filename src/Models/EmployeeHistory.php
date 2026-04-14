<?php

namespace Aliziodev\LaravelKaryawanCore\Models;

use Aliziodev\LaravelKaryawanCore\Enums\HistoryType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeHistory extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'old_value',
        'new_value',
        'effective_date',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => HistoryType::class,
            'old_value' => 'array',
            'new_value' => 'array',
            'effective_date' => 'date',
        ];
    }

    public function getTable(): string
    {
        return config('karyawan.table_prefix', '').config('karyawan.table_names.employee_histories', 'employee_histories');
    }

    // --- Relations ---

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // --- Scopes ---

    public function scopeOfType(Builder $query, HistoryType $type): void
    {
        $query->where('type', $type->value);
    }

    public function scopeLatest(Builder $query): void
    {
        $query->orderByDesc('effective_date')->orderByDesc('created_at');
    }
}
