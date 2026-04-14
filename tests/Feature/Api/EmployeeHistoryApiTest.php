<?php

use Aliziodev\LaravelKaryawanCore\Actions\ChangeEmployeeStatusAction;
use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Models\Employee;

beforeEach(function () {
    $this->employee = Employee::factory()->create(['active_status' => ActiveStatus::Active]);
});

it('GET /histories returns employee histories', function () {
    app(ChangeEmployeeStatusAction::class)->execute(
        $this->employee,
        ActiveStatus::Inactive,
        notes: 'Test'
    );

    $response = $this->getJson("/api/karyawan/employees/{$this->employee->id}/histories");

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.type', 'status_change');
});

it('GET /histories returns empty when no history', function () {
    $response = $this->getJson("/api/karyawan/employees/{$this->employee->id}/histories");

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});

it('history includes type_label', function () {
    app(ChangeEmployeeStatusAction::class)->execute(
        $this->employee,
        ActiveStatus::Inactive,
    );

    $response = $this->getJson("/api/karyawan/employees/{$this->employee->id}/histories");

    $response->assertOk();
    $response->assertJsonPath('data.0.type_label', 'Perubahan Status');
});
