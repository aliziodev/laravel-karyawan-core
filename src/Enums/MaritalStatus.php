<?php

namespace Aliziodev\LaravelKaryawanCore\Enums;

enum MaritalStatus: string
{
    case Single = 'single';
    case Married = 'married';
    case Divorced = 'divorced';
    case Widowed = 'widowed';

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Belum Menikah',
            self::Married => 'Menikah',
            self::Divorced => 'Cerai Hidup',
            self::Widowed => 'Cerai Mati',
        };
    }
}
