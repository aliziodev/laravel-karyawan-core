<?php

namespace Aliziodev\LaravelKaryawanCore\Services;

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeDocumentService
{
    /**
     * Simpan file ke storage dan kembalikan metadata file.
     * Pemanggil bertanggung jawab menyimpan metadata ke database.
     */
    public function storeFile(
        UploadedFile $file,
        Employee $employee,
        string $disk = 'local',
        string $folder = 'employee-documents',
    ): array {
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid().'.'.$extension;
        $path = "{$folder}/{$employee->employee_code}/{$fileName}";

        Storage::disk($disk)->putFileAs(
            "{$folder}/{$employee->employee_code}",
            $file,
            $fileName,
        );

        return [
            'file_disk' => $disk,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_extension' => $extension,
            'file_mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'checksum' => hash_file('sha256', $file->getRealPath()),
        ];
    }

    /**
     * Hapus file dari storage.
     */
    public function deleteFile(EmployeeDocument $document): void
    {
        if (Storage::disk($document->file_disk)->exists($document->file_path)) {
            Storage::disk($document->file_disk)->delete($document->file_path);
        }
    }

    /**
     * Dapatkan URL sementara untuk file (jika disk mendukung).
     */
    public function getTemporaryUrl(EmployeeDocument $document, int $minutes = 30): ?string
    {
        try {
            return Storage::disk($document->file_disk)
                ->temporaryUrl($document->file_path, now()->addMinutes($minutes));
        } catch (\Exception) {
            return Storage::disk($document->file_disk)->url($document->file_path);
        }
    }
}
