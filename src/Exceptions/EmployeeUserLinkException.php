<?php

namespace Aliziodev\LaravelKaryawanCore\Exceptions;

use RuntimeException;

class EmployeeUserLinkException extends RuntimeException
{
    public static function userAlreadyLinked(int $userId): self
    {
        return new self("User #{$userId} sudah terhubung ke employee lain.");
    }

    public static function employeeAlreadyHasUser(int $employeeId): self
    {
        return new self("Employee #{$employeeId} sudah memiliki akun login.");
    }

    public static function employeeHasNoUser(int $employeeId): self
    {
        return new self("Employee #{$employeeId} belum memiliki akun login.");
    }
}
