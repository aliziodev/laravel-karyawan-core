<?php

use Aliziodev\LaravelKaryawanCore\Actions\ChangeEmployeeStatusAction;
use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Events\EmployeeStatusChanged;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeHistory;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->action = app(ChangeEmployeeStatusAction::class);
});

it('can change employee status to inactive', function () {
    $employee = Employee::factory()->create(['active_status' => ActiveStatus::Active->value]);

    $updated = $this->action->execute($employee, ActiveStatus::Inactive);

    expect($updated->active_status)->toBe(ActiveStatus::Inactive);
});

it('records history when status changes', function () {
    $employee = Employee::factory()->create(['active_status' => ActiveStatus::Active->value]);

    $this->action->execute($employee, ActiveStatus::Resigned, effectiveDate: '2025-01-01', notes: 'Mengundurkan diri');

    $history = EmployeeHistory::where('employee_id', $employee->id)->first();

    expect($history->type->value)->toBe('status_change');
    expect($history->old_value['active_status'])->toBe('active');
    expect($history->new_value['active_status'])->toBe('resigned');
    expect($history->notes)->toBe('Mengundurkan diri');
});

it('sets exit_date automatically when resigned', function () {
    $employee = Employee::factory()->create([
        'active_status' => ActiveStatus::Active->value,
        'exit_date' => null,
    ]);

    $this->action->execute($employee, ActiveStatus::Resigned, effectiveDate: '2025-06-01');

    $employee->refresh();
    expect($employee->exit_date?->toDateString())->toBe('2025-06-01');
});

it('sets exit_date automatically when terminated', function () {
    $employee = Employee::factory()->create(['active_status' => ActiveStatus::Active->value, 'exit_date' => null]);

    $this->action->execute($employee, ActiveStatus::Terminated);

    $employee->refresh();
    expect($employee->exit_date)->not->toBeNull();
});

it('does not overwrite existing exit_date', function () {
    $originalExitDate = '2024-01-15';
    $employee = Employee::factory()->create([
        'active_status' => ActiveStatus::Active->value,
        'exit_date' => $originalExitDate,
    ]);

    $this->action->execute($employee, ActiveStatus::Resigned, effectiveDate: '2025-01-01');

    $employee->refresh();
    expect($employee->exit_date?->toDateString())->toBe($originalExitDate);
});

it('does nothing when status is same', function () {
    Event::fake([EmployeeStatusChanged::class]);

    $employee = Employee::factory()->create(['active_status' => ActiveStatus::Active->value]);

    $this->action->execute($employee, ActiveStatus::Active);

    Event::assertNotDispatched(EmployeeStatusChanged::class);
    expect(EmployeeHistory::where('employee_id', $employee->id)->count())->toBe(0);
});

it('dispatches EmployeeStatusChanged event', function () {
    Event::fake([EmployeeStatusChanged::class]);

    $employee = Employee::factory()->create(['active_status' => ActiveStatus::Active->value]);

    $this->action->execute($employee, ActiveStatus::Resigned);

    Event::assertDispatched(EmployeeStatusChanged::class, function (EmployeeStatusChanged $event) {
        return $event->previousStatus === ActiveStatus::Active
            && $event->newStatus === ActiveStatus::Resigned;
    });
});
