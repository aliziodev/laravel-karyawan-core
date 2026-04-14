<?php

use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeHistory;

it('scope active returns only active employees', function () {
    Employee::factory()->create(['active_status' => ActiveStatus::Active->value]);
    Employee::factory()->create(['active_status' => ActiveStatus::Resigned->value]);
    Employee::factory()->create(['active_status' => ActiveStatus::Inactive->value]);

    $active = Employee::active()->get();

    expect($active)->toHaveCount(1);
    expect($active->first()->active_status)->toBe(ActiveStatus::Active);
});

it('scope withLogin returns only employees with user_id', function () {
    Employee::factory()->create(['user_id' => null]);
    Employee::factory()->create(['user_id' => 1]);
    Employee::factory()->create(['user_id' => 2]);

    $withLogin = Employee::withLogin()->get();

    expect($withLogin)->toHaveCount(2);
});

it('scope withoutLogin returns only employees without user', function () {
    Employee::factory()->create(['user_id' => null]);
    Employee::factory()->create(['user_id' => null]);
    Employee::factory()->create(['user_id' => 5]);

    $withoutLogin = Employee::withoutLogin()->get();

    expect($withoutLogin)->toHaveCount(2);
});

it('scope byCompany filters by company_id', function () {
    $company1 = Company::factory()->create();
    $company2 = Company::factory()->create();

    Employee::factory()->create(['company_id' => $company1->id]);
    Employee::factory()->create(['company_id' => $company1->id]);
    Employee::factory()->create(['company_id' => $company2->id]);

    $results = Employee::byCompany($company1->id)->get();

    expect($results)->toHaveCount(2);
    expect($results->every(fn ($e) => $e->company_id === $company1->id))->toBeTrue();
});

it('scope search finds by full_name, code, or email', function () {
    Employee::factory()->create(['full_name' => 'Alizio', 'work_email' => 'alizio@test.com', 'employee_code' => 'EMP00001']);
    Employee::factory()->create(['full_name' => 'Rina Firgina', 'work_email' => 'rina@test.com', 'employee_code' => 'EMP00002']);

    expect(Employee::search('Alizio')->count())->toBe(1);
    expect(Employee::search('alizio@test')->count())->toBe(1);
    expect(Employee::search('EMP00002')->count())->toBe(1);
    expect(Employee::search('Firgina')->count())->toBe(1);
    expect(Employee::search('xyz-tidak-ada')->count())->toBe(0);
});

it('soft deleted employees are excluded from queries', function () {
    $employee = Employee::factory()->create();
    $employee->delete();

    expect(Employee::count())->toBe(0);
    expect(Employee::withTrashed()->count())->toBe(1);
});

it('soft delete does not break historical data', function () {
    $employee = Employee::factory()->create();

    EmployeeHistory::create([
        'employee_id' => $employee->id,
        'type' => 'status_change',
        'effective_date' => now()->toDateString(),
    ]);

    $employee->delete();

    // History tetap ada setelah soft delete
    $history = EmployeeHistory::where('employee_id', $employee->id)->get();
    expect($history)->toHaveCount(1);
});
