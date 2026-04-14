<?php

use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeHistory;

it('PATCH /api/karyawan/employees/{id}/status changes status', function () {
    $employee = Employee::factory()->create(['active_status' => ActiveStatus::Active]);

    $response = $this->patchJson("/api/karyawan/employees/{$employee->id}/status", [
        'active_status' => 'inactive',
        'effective_date' => '2025-01-01',
        'notes' => 'Cuti panjang',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.active_status', 'inactive');

    expect($employee->fresh()->active_status)->toBe(ActiveStatus::Inactive);
});

it('status change records history', function () {
    $employee = Employee::factory()->create(['active_status' => ActiveStatus::Active]);

    $this->patchJson("/api/karyawan/employees/{$employee->id}/status", [
        'active_status' => 'resigned',
    ]);

    $history = EmployeeHistory::where('employee_id', $employee->id)->first();

    expect($history)->not->toBeNull();
    expect($history->type->value)->toBe('status_change');
    expect($history->new_value['active_status'])->toBe('resigned');
});

it('sets exit_date automatically on resigned status', function () {
    $employee = Employee::factory()->create(['active_status' => ActiveStatus::Active]);

    $this->patchJson("/api/karyawan/employees/{$employee->id}/status", [
        'active_status' => 'resigned',
        'effective_date' => '2025-06-30',
    ]);

    expect($employee->fresh()->exit_date->toDateString())->toBe('2025-06-30');
});

it('noop when status is same', function () {
    $employee = Employee::factory()->create(['active_status' => ActiveStatus::Active]);

    $response = $this->patchJson("/api/karyawan/employees/{$employee->id}/status", [
        'active_status' => 'active',
    ]);

    $response->assertOk();

    expect(EmployeeHistory::where('employee_id', $employee->id)->count())->toBe(0);
});
