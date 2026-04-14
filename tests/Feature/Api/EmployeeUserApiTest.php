<?php

use Aliziodev\LaravelKaryawanCore\Exceptions\EmployeeUserLinkException;
use Aliziodev\LaravelKaryawanCore\Models\Employee;

it('POST /user links employee to user', function () {
    $employee = Employee::factory()->create(['user_id' => null]);

    $response = $this->postJson("/api/karyawan/employees/{$employee->id}/user", [
        'user_id' => 42,
    ]);

    $response->assertOk();
    expect($employee->fresh()->user_id)->toBe(42);
});

it('POST /user requires user_id', function () {
    $employee = Employee::factory()->create();

    $response = $this->postJson("/api/karyawan/employees/{$employee->id}/user", []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('user_id');
});

it('DELETE /user unlinks employee from user', function () {
    $employee = Employee::factory()->create(['user_id' => 5]);

    $response = $this->deleteJson("/api/karyawan/employees/{$employee->id}/user");

    $response->assertOk();
    $response->assertJsonPath('message', 'Akun login berhasil dilepas.');
    expect($employee->fresh()->user_id)->toBeNull();
});

it('POST /user throws exception when already linked', function () {
    $employee = Employee::factory()->create(['user_id' => 1]);

    $this->withoutExceptionHandling();

    expect(fn () => $this->postJson("/api/karyawan/employees/{$employee->id}/user", ['user_id' => 2]))
        ->toThrow(EmployeeUserLinkException::class);
});
