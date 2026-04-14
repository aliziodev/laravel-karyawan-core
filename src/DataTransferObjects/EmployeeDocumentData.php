<?php

namespace Aliziodev\LaravelKaryawanCore\DataTransferObjects;

use Aliziodev\LaravelKaryawanCore\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;

readonly class EmployeeDocumentData
{
    public function __construct(
        public DocumentType $type,
        public string $name,
        public string $file_disk,
        public string $file_path,
        public string $file_name,
        public ?string $document_number = null,
        public ?string $issued_at = null,
        public ?string $expired_at = null,
        public ?string $file_extension = null,
        public ?string $file_mime_type = null,
        public ?int $file_size = null,
        public ?string $checksum = null,
        public ?array $metadata = null,
        public ?string $notes = null,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            type: $request->enum('type', DocumentType::class),
            name: $request->string('name')->toString(),
            file_disk: $request->string('file_disk')->toString(),
            file_path: $request->string('file_path')->toString(),
            file_name: $request->string('file_name')->toString(),
            document_number: $request->input('document_number'),
            issued_at: $request->input('issued_at'),
            expired_at: $request->input('expired_at'),
            file_extension: $request->input('file_extension'),
            file_mime_type: $request->input('file_mime_type'),
            file_size: $request->input('file_size'),
            checksum: $request->input('checksum'),
            metadata: $request->input('metadata'),
            notes: $request->input('notes'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: DocumentType::from($data['type']),
            name: $data['name'],
            file_disk: $data['file_disk'],
            file_path: $data['file_path'],
            file_name: $data['file_name'],
            document_number: $data['document_number'] ?? null,
            issued_at: $data['issued_at'] ?? null,
            expired_at: $data['expired_at'] ?? null,
            file_extension: $data['file_extension'] ?? null,
            file_mime_type: $data['file_mime_type'] ?? null,
            file_size: $data['file_size'] ?? null,
            checksum: $data['checksum'] ?? null,
            metadata: $data['metadata'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type->value,
            'name' => $this->name,
            'document_number' => $this->document_number,
            'issued_at' => $this->issued_at,
            'expired_at' => $this->expired_at,
            'file_disk' => $this->file_disk,
            'file_path' => $this->file_path,
            'file_name' => $this->file_name,
            'file_extension' => $this->file_extension,
            'file_mime_type' => $this->file_mime_type,
            'file_size' => $this->file_size,
            'checksum' => $this->checksum,
            'metadata' => $this->metadata,
            'notes' => $this->notes,
        ], fn ($value) => $value !== null);
    }
}
