<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Requests\Document;

use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeDocumentData;
use Aliziodev\LaravelKaryawanCore\Enums\DocumentType;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Services\EmployeeDocumentService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(DocumentType::class)],
            'name' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'issued_at' => ['nullable', 'date'],
            'expired_at' => ['nullable', 'date', 'after_or_equal:issued_at'],

            // Jika upload file langsung
            'file' => ['nullable', 'file', 'max:10240'],

            // Jika hanya simpan metadata (file sudah ada di storage)
            'file_disk' => ['required_without:file', 'string', 'max:50'],
            'file_path' => ['required_without:file', 'string'],
            'file_name' => ['required_without:file', 'string', 'max:255'],

            'notes' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => __('karyawan::validation.common.required'),
            '*.required_without' => __('karyawan::validation.common.required_without'),
            '*.string' => __('karyawan::validation.common.string'),
            '*.array' => __('karyawan::validation.common.array'),
            '*.file' => __('karyawan::validation.common.file'),
            '*.date' => __('karyawan::validation.common.date'),
            '*.after_or_equal' => __('karyawan::validation.common.after_or_equal'),
            '*.enum' => __('karyawan::validation.common.enum'),
            'name.max' => __('karyawan::validation.common.max'),
            'document_number.max' => __('karyawan::validation.common.max'),
            'file_disk.max' => __('karyawan::validation.common.max'),
            'file_name.max' => __('karyawan::validation.common.max'),
            'notes.max' => __('karyawan::validation.common.max'),
            'file.max' => __('karyawan::validation.common.max_file'),
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => __('karyawan::validation.attributes.type'),
            'name' => __('karyawan::validation.attributes.name'),
            'document_number' => __('karyawan::validation.attributes.document_number'),
            'issued_at' => __('karyawan::validation.attributes.issued_at'),
            'expired_at' => __('karyawan::validation.attributes.expired_at'),
            'file' => __('karyawan::validation.attributes.file'),
            'file_disk' => __('karyawan::validation.attributes.file_disk'),
            'file_path' => __('karyawan::validation.attributes.file_path'),
            'file_name' => __('karyawan::validation.attributes.file_name'),
            'notes' => __('karyawan::validation.attributes.notes'),
            'metadata' => __('karyawan::validation.attributes.metadata'),
        ];
    }

    /**
     * Konversi request ke EmployeeDocumentData.
     * Logika pemilihan file-upload vs metadata-only ada di sini (HTTP layer),
     * bukan di controller, bukan di action.
     */
    public function toData(Employee $employee): EmployeeDocumentData
    {
        if ($this->hasFile('file')) {
            /** @var EmployeeDocumentService $service */
            $service = app(EmployeeDocumentService::class);
            $disk = $this->input('file_disk', config('filesystems.default', 'local'));
            $fileMeta = $service->storeFile($this->file('file'), $employee, $disk);

            return new EmployeeDocumentData(
                type: DocumentType::from($this->validated('type')),
                name: $this->string('name')->toString(),
                file_disk: $fileMeta['file_disk'],
                file_path: $fileMeta['file_path'],
                file_name: $fileMeta['file_name'],
                document_number: $this->input('document_number'),
                issued_at: $this->input('issued_at'),
                expired_at: $this->input('expired_at'),
                file_extension: $fileMeta['file_extension'],
                file_mime_type: $fileMeta['file_mime_type'],
                file_size: $fileMeta['file_size'],
                checksum: $fileMeta['checksum'],
                metadata: $this->input('metadata'),
                notes: $this->input('notes'),
            );
        }

        return EmployeeDocumentData::fromRequest($this);
    }
}
