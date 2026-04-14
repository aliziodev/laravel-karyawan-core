<?php

use Aliziodev\LaravelKaryawanCore\Actions\CreateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData;
use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Events\EmployeeCreated;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Event;

it('can create employee without user', function () {
    $action = app(CreateEmployeeAction::class);

    $employee = $action->execute(EmployeeData::fromArray([
        'full_name' => 'Alizio',
    ]));

    expect($employee)->toBeInstanceOf(Employee::class);
    expect($employee->full_name)->toBe('Alizio');
    expect($employee->user_id)->toBeNull();
    expect($employee->active_status)->toBe(ActiveStatus::Active);
    expect($employee->employee_code)->toStartWith('EMP');
});

it('auto-generates employee code when not provided', function () {
    $action = app(CreateEmployeeAction::class);

    $emp1 = $action->execute(EmployeeData::fromArray(['full_name' => 'Karyawan Satu']));
    $emp2 = $action->execute(EmployeeData::fromArray(['full_name' => 'Karyawan Dua']));

    expect($emp1->employee_code)->toBe('EMP00001');
    expect($emp2->employee_code)->toBe('EMP00002');
});

it('uses provided employee code when given', function () {
    $action = app(CreateEmployeeAction::class);

    $employee = $action->execute(EmployeeData::fromArray([
        'full_name' => 'Alizio',
        'employee_code' => 'CUSTOM-001',
    ]));

    expect($employee->employee_code)->toBe('CUSTOM-001');
});

it('dispatches EmployeeCreated event after creation', function () {
    Event::fake([EmployeeCreated::class]);

    $action = app(CreateEmployeeAction::class);
    $action->execute(EmployeeData::fromArray(['full_name' => 'Alizio']));

    Event::assertDispatched(EmployeeCreated::class, function (EmployeeCreated $event) {
        return $event->employee->full_name === 'Alizio';
    });
});

it('employee_code is unique in database', function () {
    Employee::factory()->create(['employee_code' => 'EMP00001']);

    expect(fn () => Employee::factory()->create(['employee_code' => 'EMP00001']))
        ->toThrow(QueryException::class);
});

it('work_email must be unique', function () {
    Employee::factory()->create(['work_email' => 'budi@company.com']);

    expect(fn () => Employee::factory()->create(['work_email' => 'budi@company.com']))
        ->toThrow(QueryException::class);
});

it('persists all provided fields correctly', function () {
    $action = app(CreateEmployeeAction::class);

    $employee = $action->execute(EmployeeData::fromArray([
        'full_name' => 'Rina Firgina',
        'work_email' => 'siti@company.com',
        'phone' => '08123456789',
        'citizenship' => 'WNI',
        'employment_type' => 'permanent',
        'active_status' => 'active',
    ]));

    $employee->refresh();

    expect($employee->full_name)->toBe('Rina Firgina');
    expect($employee->work_email)->toBe('siti@company.com');
    expect($employee->citizenship)->toBe('WNI');
});
