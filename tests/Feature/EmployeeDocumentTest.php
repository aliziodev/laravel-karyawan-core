<?php

use Aliziodev\LaravelKaryawanCore\Actions\DeleteEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\Actions\StoreEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeDocumentData;
use Aliziodev\LaravelKaryawanCore\Enums\DocumentType;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');

    $this->storeAction = app(StoreEmployeeDocumentAction::class);
    $this->deleteAction = app(DeleteEmployeeDocumentAction::class);
});

it('can store employee document metadata', function () {
    $employee = Employee::factory()->create();

    $document = $this->storeAction->execute($employee, new EmployeeDocumentData(
        type: DocumentType::Ktp,
        name: 'KTP Alizio',
        file_disk: 'local',
        file_path: 'employee-documents/EMP00001/ktp.pdf',
        file_name: 'ktp.pdf',
    ));

    expect($document)->toBeInstanceOf(EmployeeDocument::class);
    expect($document->employee_id)->toBe($employee->id);
    expect($document->type)->toBe(DocumentType::Ktp);
    expect($document->name)->toBe('KTP Alizio');
    expect($document->file_disk)->toBe('local');
});

it('stores document with full metadata', function () {
    $employee = Employee::factory()->create();

    $document = $this->storeAction->execute($employee, new EmployeeDocumentData(
        type: DocumentType::Npwp,
        name: 'NPWP',
        file_disk: 'local',
        file_path: 'employee-documents/EMP00001/npwp.pdf',
        file_name: 'npwp.pdf',
        document_number: '12.345.678.9-001.000',
        issued_at: '2020-01-01',
        expired_at: null,
        file_extension: 'pdf',
        file_mime_type: 'application/pdf',
        file_size: 102400,
        metadata: ['verified' => true],
        notes: 'NPWP aktif',
    ));

    expect($document->document_number)->toBe('12.345.678.9-001.000');
    expect($document->file_size)->toBe(102400);
    expect($document->metadata)->toBe(['verified' => true]);
});

it('document is accessible via employee relation', function () {
    $employee = Employee::factory()->create();

    $this->storeAction->execute($employee, new EmployeeDocumentData(
        type: DocumentType::Contract,
        name: 'Kontrak Kerja',
        file_disk: 'local',
        file_path: 'docs/contract.pdf',
        file_name: 'contract.pdf',
    ));

    expect($employee->documents()->count())->toBe(1);
    expect($employee->documents()->first()->type)->toBe(DocumentType::Contract);
});

it('can delete employee document', function () {
    $employee = Employee::factory()->create();
    $document = $this->storeAction->execute($employee, new EmployeeDocumentData(
        type: DocumentType::Kk,
        name: 'Kartu Keluarga',
        file_disk: 'local',
        file_path: 'docs/kk.pdf',
        file_name: 'kk.pdf',
    ));

    $documentId = $document->id;

    $this->deleteAction->execute($document);

    expect(EmployeeDocument::find($documentId))->toBeNull();
});

it('document deletion is deleted from database', function () {
    $employee = Employee::factory()->create();
    $document = $this->storeAction->execute($employee, new EmployeeDocumentData(
        type: DocumentType::Ktp,
        name: 'KTP',
        file_disk: 'local',
        file_path: 'docs/ktp.pdf',
        file_name: 'ktp.pdf',
    ));

    $documentId = $document->id;
    $this->deleteAction->execute($document);

    expect(EmployeeDocument::find($documentId))->toBeNull();
    expect($employee->documents()->count())->toBe(0);
});

it('isExpired returns true for past expired_at', function () {
    $document = new EmployeeDocument([
        'expired_at' => now()->subDay(),
    ]);

    expect($document->isExpired())->toBeTrue();
});

it('isExpired returns false for future expired_at', function () {
    $document = new EmployeeDocument([
        'expired_at' => now()->addYear(),
    ]);

    expect($document->isExpired())->toBeFalse();
});
