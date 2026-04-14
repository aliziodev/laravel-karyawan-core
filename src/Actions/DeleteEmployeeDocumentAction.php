<?php

namespace Aliziodev\LaravelKaryawanCore\Actions;

use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;
use Aliziodev\LaravelKaryawanCore\Services\EmployeeDocumentService;
use Illuminate\Support\Facades\DB;

class DeleteEmployeeDocumentAction
{
    public function __construct(
        private readonly EmployeeDocumentService $documentService,
    ) {}

    /**
     * Hapus dokumen: file dari storage dan record dari database.
     * Kedua operasi dibungkus transaction: jika DB delete gagal, file tidak terhapus.
     * (Urutan: delete DB dulu, lalu hapus file — agar tidak ada orphan record.)
     */
    public function execute(EmployeeDocument $document): void
    {
        $fileDisk = $document->file_disk;
        $filePath = $document->file_path;

        DB::transaction(function () use ($document) {
            $document->delete();
        });

        // Hapus file setelah transaction commit agar konsisten
        $this->documentService->deleteFile(
            new EmployeeDocument(['file_disk' => $fileDisk, 'file_path' => $filePath])
        );
    }
}
