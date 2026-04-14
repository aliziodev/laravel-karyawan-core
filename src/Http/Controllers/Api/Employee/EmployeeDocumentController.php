<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee;

use Aliziodev\LaravelKaryawanCore\Actions\DeleteEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\Actions\StoreEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Document\StoreDocumentRequest;
use Aliziodev\LaravelKaryawanCore\Http\Resources\EmployeeDocumentResource;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class EmployeeDocumentController extends Controller
{
    public function __construct(
        private readonly StoreEmployeeDocumentAction $storeDocumentAction,
        private readonly DeleteEmployeeDocumentAction $deleteDocumentAction,
    ) {}

    public function index(Employee $employee): AnonymousResourceCollection
    {
        return EmployeeDocumentResource::collection(
            $employee->documents()->latest()->get()
        );
    }

    public function store(StoreDocumentRequest $request, Employee $employee): JsonResponse
    {
        $document = $this->storeDocumentAction->execute($employee, $request->toData($employee));

        return (new EmployeeDocumentResource($document))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Employee $employee, EmployeeDocument $document): JsonResponse
    {
        abort_unless($document->employee_id === $employee->id, 404);

        $this->deleteDocumentAction->execute($document);

        return response()->json(['message' => 'Dokumen berhasil dihapus.']);
    }
}
