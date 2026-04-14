<?php

use Aliziodev\LaravelKaryawanCore\Actions\LinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Actions\UnlinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Events\EmployeeLinkedToUser;
use Aliziodev\LaravelKaryawanCore\Events\EmployeeUnlinkedFromUser;
use Aliziodev\LaravelKaryawanCore\Exceptions\EmployeeUserLinkException;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeHistory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->linkAction = app(LinkEmployeeUserAction::class);
    $this->unlinkAction = app(UnlinkEmployeeUserAction::class);
});

it('can link employee to user', function () {
    $employee = Employee::factory()->create();

    $updated = $this->linkAction->execute($employee, userId: 1);

    expect($updated->user_id)->toBe(1);
    expect($updated->hasLogin())->toBeTrue();
});

it('records history when user is linked', function () {
    $employee = Employee::factory()->create();

    $this->linkAction->execute($employee, userId: 1, createdBy: 99);

    $history = EmployeeHistory::where('employee_id', $employee->id)->first();

    expect($history)->not->toBeNull();
    expect($history->type->value)->toBe('user_linked');
    expect($history->new_value['user_id'])->toBe(1);
    expect($history->created_by)->toBe(99);
});

it('throws exception if employee already has user', function () {
    $employee = Employee::factory()->create(['user_id' => 1]);

    expect(fn () => $this->linkAction->execute($employee, userId: 2))
        ->toThrow(EmployeeUserLinkException::class);
});

it('throws exception if user is already linked to another employee', function () {
    Employee::factory()->create(['user_id' => 5]);
    $another = Employee::factory()->create();

    expect(fn () => $this->linkAction->execute($another, userId: 5))
        ->toThrow(EmployeeUserLinkException::class);
});

it('user_id has unique constraint in database', function () {
    Employee::factory()->create(['user_id' => 10]);

    expect(fn () => Employee::factory()->create(['user_id' => 10]))
        ->toThrow(QueryException::class);
});

it('dispatches EmployeeLinkedToUser event', function () {
    Event::fake([EmployeeLinkedToUser::class]);

    $employee = Employee::factory()->create();
    $this->linkAction->execute($employee, userId: 7);

    Event::assertDispatched(EmployeeLinkedToUser::class, function (EmployeeLinkedToUser $event) {
        return $event->userId === 7;
    });
});

it('can unlink employee from user', function () {
    $employee = Employee::factory()->create(['user_id' => 3]);

    $updated = $this->unlinkAction->execute($employee);

    expect($updated->user_id)->toBeNull();
    expect($updated->hasLogin())->toBeFalse();
});

it('records history when user is unlinked', function () {
    $employee = Employee::factory()->create(['user_id' => 3]);

    $this->unlinkAction->execute($employee);

    $history = EmployeeHistory::where('employee_id', $employee->id)
        ->where('type', 'user_unlinked')
        ->first();

    expect($history)->not->toBeNull();
    expect($history->old_value['user_id'])->toBe(3);
});

it('throws exception when trying to unlink employee that has no user', function () {
    $employee = Employee::factory()->create(['user_id' => null]);

    expect(fn () => $this->unlinkAction->execute($employee))
        ->toThrow(EmployeeUserLinkException::class);
});

it('dispatches EmployeeUnlinkedFromUser event', function () {
    Event::fake([EmployeeUnlinkedFromUser::class]);

    $employee = Employee::factory()->create(['user_id' => 8]);
    $this->unlinkAction->execute($employee);

    Event::assertDispatched(EmployeeUnlinkedFromUser::class);
});
