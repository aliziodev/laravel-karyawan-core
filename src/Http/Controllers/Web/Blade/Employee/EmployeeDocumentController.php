<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\Employee;

use Aliziodev\LaravelKaryawanCore\Actions\DeleteEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\Actions\StoreEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Document\StoreDocumentRequest;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class EmployeeDocumentController extends Controller
{
    public function __construct(
        private readonly StoreEmployeeDocumentAction $storeDocumentAction,
        private readonly DeleteEmployeeDocumentAction $deleteDocumentAction,
    ) {}

    public function index(Employee $employee): View
    {
        return view('karyawan.employee.document.index', [
            'employee' => $employee,
            'documents' => $employee->documents()->latest()->get(),
        ]);
    }

    public function store(StoreDocumentRequest $request, Employee $employee): RedirectResponse
    {
        $this->storeDocumentAction->execute($employee, $request->toData($employee));

        return back()->with('success', 'Dokumen berhasil disimpan.');
    }

    public function destroy(Employee $employee, EmployeeDocument $document): RedirectResponse
    {
        abort_unless($document->employee_id === $employee->id, 404);

        $this->deleteDocumentAction->execute($document);

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
