<?php

namespace Aliziodev\LaravelKaryawanCore\Events;

use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeStatusChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Employee $employee,
        public readonly ActiveStatus $previousStatus,
        public readonly ActiveStatus $newStatus,
    ) {}
}
