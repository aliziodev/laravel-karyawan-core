<?php

use Aliziodev\LaravelKaryawanCore\Actions\UpdateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData;
use Aliziodev\LaravelKaryawanCore\Events\EmployeeUpdated;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Support\Facades\Event;

it('can update employee data', function () {
    $employee = Employee::factory()->create(['full_name' => 'Nama Lama']);
    $action = app(UpdateEmployeeAction::class);

    $updated = $action->execute($employee, EmployeeData::fromArray([
        'full_name' => 'Nama Baru',
    ]));

    expect($updated->full_name)->toBe('Nama Baru');
});

it('cannot update employee_code via update action', function () {
    $originalCode = 'EMP00001';
    $employee = Employee::factory()->create(['employee_code' => $originalCode]);
    $action = app(UpdateEmployeeAction::class);

    $action->execute($employee, EmployeeData::fromArray([
        'full_name' => 'Updated Name',
        'employee_code' => 'NEW-CODE-999', // harus diabaikan
    ]));

    $employee->refresh();
    expect($employee->employee_code)->toBe($originalCode);
});

it('dispatches EmployeeUpdated event with changed attributes', function () {
    Event::fake([EmployeeUpdated::class]);

    $employee = Employee::factory()->create(['full_name' => 'Nama Lama', 'nick_name' => null]);
    $action = app(UpdateEmployeeAction::class);

    $action->execute($employee, EmployeeData::fromArray([
        'full_name' => 'Nama Baru',
        'nick_name' => 'Budi',
    ]));

    Event::assertDispatched(EmployeeUpdated::class, function (EmployeeUpdated $event) {
        return isset($event->changedAttributes['full_name']);
    });
});

it('does not dispatch event when nothing changed', function () {
    Event::fake([EmployeeUpdated::class]);

    $employee = Employee::factory()->create(['full_name' => 'Nama Sama']);
    $action = app(UpdateEmployeeAction::class);

    $action->execute($employee, EmployeeData::fromArray(['full_name' => 'Nama Sama']));

    Event::assertNotDispatched(EmployeeUpdated::class);
});
