<?php

namespace Aliziodev\LaravelKaryawanCore\Exceptions;

use RuntimeException;

class EmployeeCodeGenerationException extends RuntimeException
{
    public static function failedToGenerate(string $prefix): self
    {
        return new self("Gagal generate employee code dengan prefix '{$prefix}'.");
    }
}
