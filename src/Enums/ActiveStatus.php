<?php

namespace Aliziodev\LaravelKaryawanCore\Enums;

enum ActiveStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Resigned = 'resigned';
    case Terminated = 'terminated';
    case Retired = 'retired';
    case LongLeave = 'long_leave';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Inactive => 'Tidak Aktif',
            self::Resigned => 'Mengundurkan Diri',
            self::Terminated => 'PHK',
            self::Retired => 'Pensiun',
            self::LongLeave => 'Cuti Panjang',
        };
    }

    public function isWorking(): bool
    {
        return in_array($this, [self::Active, self::LongLeave]);
    }
}
