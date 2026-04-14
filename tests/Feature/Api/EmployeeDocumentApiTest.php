<?php

use Aliziodev\LaravelKaryawanCore\Actions\StoreEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeDocumentData;
use Aliziodev\LaravelKaryawanCore\Enums\DocumentType;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;

beforeEach(function () {
    $this->employee = Employee::factory()->create();
});

it('GET /documents returns employee documents', function () {
    app(StoreEmployeeDocumentAction::class)->execute($this->employee, new EmployeeDocumentData(
        type: DocumentType::Ktp,
        name: 'KTP',
        file_disk: 'local',
        file_path: 'docs/ktp.pdf',
        file_name: 'ktp.pdf',
    ));

    $response = $this->getJson("/api/karyawan/employees/{$this->employee->id}/documents");

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'KTP');
});

it('POST /documents stores document metadata', function () {
    $response = $this->postJson("/api/karyawan/employees/{$this->employee->id}/documents", [
        'type' => 'ktp',
        'name' => 'KTP Test',
        'file_disk' => 'local',
        'file_path' => 'docs/ktp.pdf',
        'file_name' => 'ktp.pdf',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.name', 'KTP Test');
    $response->assertJsonPath('data.type', 'ktp');
});

it('POST /documents requires type and name', function () {
    $response = $this->postJson("/api/karyawan/employees/{$this->employee->id}/documents", []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['type', 'name']);
});

it('DELETE /documents/{id} deletes document', function () {
    $document = app(StoreEmployeeDocumentAction::class)->execute($this->employee, new EmployeeDocumentData(
        type: DocumentType::Ktp,
        name: 'KTP',
        file_disk: 'local',
        file_path: 'docs/ktp.pdf',
        file_name: 'ktp.pdf',
    ));

    $response = $this->deleteJson(
        "/api/karyawan/employees/{$this->employee->id}/documents/{$document->id}"
    );

    $response->assertOk();
    expect(EmployeeDocument::find($document->id))->toBeNull();
});

it('DELETE /documents/{id} returns 404 for wrong employee', function () {
    $otherEmployee = Employee::factory()->create();
    $document = app(StoreEmployeeDocumentAction::class)->execute($otherEmployee, new EmployeeDocumentData(
        type: DocumentType::Ktp,
        name: 'KTP',
        file_disk: 'local',
        file_path: 'docs/ktp.pdf',
        file_name: 'ktp.pdf',
    ));

    $response = $this->deleteJson(
        "/api/karyawan/employees/{$this->employee->id}/documents/{$document->id}"
    );

    $response->assertNotFound();
});
