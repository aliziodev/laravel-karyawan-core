<?php

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Services\EmployeeCodeGenerator;

it('generates employee code with default prefix', function () {
    $generator = new EmployeeCodeGenerator;
    $code = $generator->generate();

    expect($code)->toStartWith('EMP');
    expect($code)->toBe('EMP00001');
});

it('generates sequential codes for multiple employees', function () {
    $generator = new EmployeeCodeGenerator;

    $code1 = $generator->generate();
    Employee::factory()->create(['employee_code' => $code1]);

    $code2 = $generator->generate();

    expect($code1)->toBe('EMP00001');
    expect($code2)->toBe('EMP00002');
});

it('skips codes already used by soft-deleted employees', function () {
    $generator = new EmployeeCodeGenerator;

    // Buat employee lalu soft delete
    $employee = Employee::factory()->create(['employee_code' => 'EMP00001']);
    $employee->delete();

    // Generator harus melewati EMP00001 yang sudah ada (termasuk trashed)
    $code = $generator->generate();

    expect($code)->toBe('EMP00002');
});

it('generates code with custom prefix', function () {
    $generator = new EmployeeCodeGenerator;
    $code = $generator->generate('STAFF');

    expect($code)->toStartWith('STAFF');
    expect(strlen($code))->toBe(10); // 'STAFF' (5) + '00001' (5)
});

it('generates code using config prefix', function () {
    config()->set('karyawan.employee_code.prefix', 'KRY');
    config()->set('karyawan.employee_code.pad_length', 4);

    $generator = new EmployeeCodeGenerator;
    $code = $generator->generate();

    expect($code)->toBe('KRY0001');
});
