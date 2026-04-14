<?php

namespace Aliziodev\LaravelKaryawanCore\Enums;

enum DocumentType: string
{
    case Ktp = 'ktp';
    case Kk = 'kk';
    case Npwp = 'npwp';
    case Contract = 'contract';
    case Certificate = 'certificate';
    case Diploma = 'diploma';
    case Cv = 'cv';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Ktp => 'KTP',
            self::Kk => 'Kartu Keluarga',
            self::Npwp => 'NPWP',
            self::Contract => 'Kontrak Kerja',
            self::Certificate => 'Sertifikat',
            self::Diploma => 'Ijazah',
            self::Cv => 'CV',
            self::Other => 'Lainnya',
        };
    }
}
