<?php

namespace Aliziodev\LaravelKaryawanCore\Policies;

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeDocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(mixed $user, Employee $employee): bool
    {
        return true;
    }

    public function view(mixed $user, EmployeeDocument $document): bool
    {
        return true;
    }

    public function create(mixed $user, Employee $employee): bool
    {
        return true;
    }

    public function delete(mixed $user, EmployeeDocument $document): bool
    {
        return true;
    }
}
