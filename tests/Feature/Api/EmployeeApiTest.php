<?php

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

it('GET /api/karyawan/employees/export returns downloadable xlsx', function () {
    Employee::factory()->create(['full_name' => 'Alizio']);

    $response = $this->get('/api/karyawan/employees/export');

    $response->assertOk();
    $response->assertDownload();
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

it('GET /api/karyawan/employees/export supports filters by status and join date', function () {
    Employee::factory()->create([
        'full_name' => 'Filter Cocok',
        'active_status' => 'active',
        'join_date' => '2026-01-15',
    ]);

    Employee::factory()->create([
        'full_name' => 'Status Tidak Cocok',
        'active_status' => 'inactive',
        'join_date' => '2026-01-15',
    ]);

    Employee::factory()->create([
        'full_name' => 'Tanggal Tidak Cocok',
        'active_status' => 'active',
        'join_date' => '2024-01-01',
    ]);

    $response = $this->get('/api/karyawan/employees/export?active_status=active&join_date_from=2026-01-01&join_date_to=2026-12-31');

    $response->assertOk();
    $response->assertDownload();

    $file = $response->baseResponse->getFile()->getPathname();
    $sheet = IOFactory::load($file)->getActiveSheet();

    expect($sheet->getCell('A2')->getValue())->toBeString();
    expect($sheet->getCell('B2')->getValue())->toBe('Filter Cocok');
    expect($sheet->getCell('B3')->getValue())->toBeNull();
});

it('GET /api/karyawan/employees/export supports filters by created at date range', function () {
    Employee::factory()->create([
        'full_name' => 'Created At Cocok',
        'created_at' => '2026-04-10 09:00:00',
    ]);

    Employee::factory()->create([
        'full_name' => 'Created At Tidak Cocok',
        'created_at' => '2024-04-10 09:00:00',
    ]);

    $response = $this->get('/api/karyawan/employees/export?created_at_from=2026-01-01&created_at_to=2026-12-31');

    $response->assertOk();
    $response->assertDownload();

    $file = $response->baseResponse->getFile()->getPathname();
    $sheet = IOFactory::load($file)->getActiveSheet();

    expect($sheet->getCell('B2')->getValue())->toBe('Created At Cocok');
    expect($sheet->getCell('B3')->getValue())->toBeNull();
});

it('GET /api/karyawan/employees/export returns english validation message for invalid status', function () {
    app()->setLocale('en');

    $response = $this->getJson('/api/karyawan/employees/export?active_status=status-invalid');

    $response->assertStatus(422);
    $response->assertJsonPath('errors.active_status.0', 'The selected active status is invalid.');
});

it('GET /api/karyawan/employees/export returns indonesian validation message for invalid company id', function () {
    app()->setLocale('id');

    $response = $this->getJson('/api/karyawan/employees/export?company_id=abc');

    $response->assertStatus(422);
    $response->assertJsonPath('errors.company_id.0', 'Kolom id perusahaan harus berupa angka.');
});
