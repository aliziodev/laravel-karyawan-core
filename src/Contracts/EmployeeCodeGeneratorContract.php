<?php

namespace Aliziodev\LaravelKaryawanCore\Contracts;

interface EmployeeCodeGeneratorContract
{
    /**
     * Generate employee code yang unik, aman dari race condition.
     */
    public function generate(?string $prefix = null): string;
}
