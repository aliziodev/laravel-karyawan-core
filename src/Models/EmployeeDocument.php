<?php

namespace Aliziodev\LaravelKaryawanCore\Models;

use Aliziodev\LaravelKaryawanCore\Enums\DocumentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'name',
        'document_number',
        'issued_at',
        'expired_at',
        'file_disk',
        'file_path',
        'file_name',
        'file_extension',
        'file_mime_type',
        'file_size',
        'checksum',
        'metadata',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'issued_at' => 'date',
            'expired_at' => 'date',
            'metadata' => 'array',
            'file_size' => 'integer',
        ];
    }

    public function getTable(): string
    {
        return config('karyawan.table_prefix', '').config('karyawan.table_names.employee_documents', 'employee_documents');
    }

    // --- Relations ---

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // --- Scopes ---

    public function scopeOfType(Builder $query, DocumentType $type): void
    {
        $query->where('type', $type->value);
    }

    public function scopeExpired(Builder $query): void
    {
        $query->whereNotNull('expired_at')->where('expired_at', '<', now());
    }

    public function scopeExpiringWithin(Builder $query, int $days): void
    {
        $query->whereNotNull('expired_at')
            ->whereBetween('expired_at', [now(), now()->addDays($days)]);
    }

    // --- Helpers ---

    public function isExpired(): bool
    {
        return $this->expired_at !== null && $this->expired_at->isPast();
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if ($this->file_size === null) {
            return '-';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2).' '.$units[$unit];
    }
}
