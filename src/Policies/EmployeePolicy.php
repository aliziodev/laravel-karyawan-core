<?php

namespace Aliziodev\LaravelKaryawanCore\Policies;

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy dasar untuk Employee.
 * Host app dapat meng-extend atau mengganti policy ini sesuai kebutuhan
 * role/permission yang dipakai (Spatie, Bouncer, dll).
 */
class EmployeePolicy
{
    use HandlesAuthorization;

    public function viewAny(mixed $user): bool
    {
        return true;
    }

    public function view(mixed $user, Employee $employee): bool
    {
        return true;
    }

    /**
     * Akses ke data sensitif: NIK, KK, NPWP.
     * Override ini di host app sesuai role yang diizinkan.
     */
    public function viewSensitive(mixed $user, Employee $employee): bool
    {
        return false;
    }

    public function create(mixed $user): bool
    {
        return true;
    }

    public function update(mixed $user, Employee $employee): bool
    {
        return true;
    }

    public function delete(mixed $user, Employee $employee): bool
    {
        return true;
    }

    public function changeStatus(mixed $user, Employee $employee): bool
    {
        return true;
    }

    public function linkUser(mixed $user, Employee $employee): bool
    {
        return true;
    }

    public function unlinkUser(mixed $user, Employee $employee): bool
    {
        return true;
    }
}
