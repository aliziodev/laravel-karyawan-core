<?php

namespace Aliziodev\LaravelKaryawanCore\Models;

use Aliziodev\LaravelKaryawanCore\Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    protected static function newFactory(): PositionFactory
    {
        return PositionFactory::new();
    }

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getTable(): string
    {
        return config('karyawan.table_prefix', '').config('karyawan.table_names.positions', 'positions');
    }

    // --- Relations ---

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    // --- Scopes ---

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeByCompany(Builder $query, int $companyId): void
    {
        $query->where('company_id', $companyId);
    }
}
