<?php

namespace Aliziodev\LaravelKaryawanCore\Enums;

enum EmploymentType: string
{
    case Permanent = 'permanent';
    case Contract = 'contract';
    case Internship = 'internship';
    case DailyWorker = 'daily_worker';
    case Outsourcing = 'outsourcing';

    public function label(): string
    {
        return match ($this) {
            self::Permanent => 'Karyawan Tetap',
            self::Contract => 'Karyawan Kontrak',
            self::Internship => 'Magang',
            self::DailyWorker => 'Pekerja Harian Lepas',
            self::Outsourcing => 'Outsourcing',
        };
    }
}
