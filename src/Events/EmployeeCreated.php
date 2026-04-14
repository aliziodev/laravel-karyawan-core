<?php

namespace Aliziodev\LaravelKaryawanCore\Events;

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Employee $employee,
    ) {}
}
