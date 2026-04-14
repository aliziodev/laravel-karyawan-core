<?php

use Aliziodev\LaravelKaryawanCore\Models\Employee;

it('GET /api/karyawan/employees returns paginated employees', function () {
    Employee::factory()->count(3)->create();

    $response = $this->getJson('/api/karyawan/employees');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [['id', 'employee_code', 'full_name']],
        'meta' => ['total'],
    ]);
});

it('POST /api/karyawan/employees creates employee', function () {
    $response = $this->postJson('/api/karyawan/employees', [
        'full_name' => 'Alizio',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.full_name', 'Alizio');
    $response->assertJsonPath('data.employee_code', 'EMP00001');
});

it('GET /api/karyawan/employees/{id} returns employee detail', function () {
    $employee = Employee::factory()->create(['full_name' => 'Rina Firgina']);

    $response = $this->getJson("/api/karyawan/employees/{$employee->id}");

    $response->assertOk();
    $response->assertJsonPath('data.full_name', 'Rina Firgina');
});

it('PUT /api/karyawan/employees/{id} updates employee', function () {
    $employee = Employee::factory()->create(['full_name' => 'Lama']);

    $response = $this->putJson("/api/karyawan/employees/{$employee->id}", [
        'full_name' => 'Baru',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.full_name', 'Baru');
});

it('DELETE /api/karyawan/employees/{id} soft-deletes employee', function () {
    $employee = Employee::factory()->create();

    $response = $this->deleteJson("/api/karyawan/employees/{$employee->id}");

    $response->assertOk();
    $response->assertJsonPath('message', 'Karyawan berhasil dihapus.');

    expect(Employee::find($employee->id))->toBeNull();
    expect(Employee::withTrashed()->find($employee->id))->not->toBeNull();
});

it('GET /api/karyawan/employees supports search filter', function () {
    Employee::factory()->create(['full_name' => 'Alizio']);
    Employee::factory()->create(['full_name' => 'Rina Firgina']);

    $response = $this->getJson('/api/karyawan/employees?search=Alizio');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.full_name', 'Alizio');
});

it('GET /api/karyawan/employees supports active_only filter', function () {
    Employee::factory()->create(['active_status' => 'active']);
    Employee::factory()->create(['active_status' => 'inactive']);

    $response = $this->getJson('/api/karyawan/employees?active_only=1');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});
