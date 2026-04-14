<?php

namespace Aliziodev\LaravelKaryawanCore\Models;

use Aliziodev\LaravelKaryawanCore\Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected static function newFactory(): CompanyFactory
    {
        return CompanyFactory::new();
    }

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
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
        return config('karyawan.table_prefix', '').config('karyawan.table_names.companies', 'companies');
    }

    // --- Relations ---

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
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
}
