<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade;

use Aliziodev\LaravelKaryawanCore\Actions\CreateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\Actions\UpdateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData;
use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Enums\DocumentType;
use Aliziodev\LaravelKaryawanCore\Enums\EmploymentType;
use Aliziodev\LaravelKaryawanCore\Enums\Gender;
use Aliziodev\LaravelKaryawanCore\Enums\MaritalStatus;
use Aliziodev\LaravelKaryawanCore\Enums\Religion;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Employee\CreateEmployeeRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Employee\UpdateEmployeeRequest;
use Aliziodev\LaravelKaryawanCore\Models\Branch;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Aliziodev\LaravelKaryawanCore\Models\Department;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly CreateEmployeeAction $createAction,
        private readonly UpdateEmployeeAction $updateAction,
    ) {}

    public function index(Request $request): View
    {
        $employees = Employee::query()
            ->with(['company', 'branch', 'department', 'position'])
            ->when($request->filled('search'), fn ($q) => $q->search($request->string('search')->toString()))
            ->when($request->filled('company_id'), fn ($q) => $q->byCompany((int) $request->company_id))
            ->when($request->filled('department_id'), fn ($q) => $q->byDepartment((int) $request->department_id))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->paginate(20)
            ->withQueryString();

        return view('karyawan.employee.index', [
            'employees' => $employees,
            'filters' => $request->only(['search', 'company_id', 'department_id', 'active_only']),
            'companies' => Company::active()->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('karyawan.employee.create', $this->formOptions());
    }

    public function store(CreateEmployeeRequest $request): RedirectResponse
    {
        $employee = $this->createAction->execute(EmployeeData::fromRequest($request));

        return redirect()
            ->route('karyawan.employees.show', $employee)
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee): View
    {
        return view('karyawan.employee.show', [
            'employee' => $employee->load([
                'company',
                'branch',
                'department',
                'position',
                'manager',
                'documents',
                'emergencyContacts',
                'histories',
            ]),
        ]);
    }

    public function edit(Employee $employee): View
    {
        return view('karyawan.employee.edit', array_merge(
            ['employee' => $employee->load(['company', 'branch', 'department', 'position'])],
            $this->formOptions(),
        ));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->updateAction->execute($employee, EmployeeData::fromRequest($request));

        return redirect()
            ->route('karyawan.employees.show', $employee)
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()
            ->route('karyawan.employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }

    private function formOptions(): array
    {
        return [
            'companies' => Company::active()->get(['id', 'name', 'code']),
            'branches' => Branch::active()->get(['id', 'company_id', 'name', 'code']),
            'departments' => Department::active()->get(['id', 'company_id', 'name', 'code']),
            'positions' => Position::active()->get(['id', 'company_id', 'name', 'code']),
            'genders' => collect(Gender::cases())->map(fn ($e) => ['value' => $e->value, 'label' => $e->label()]),
            'religions' => collect(Religion::cases())->map(fn ($e) => ['value' => $e->value, 'label' => $e->label()]),
            'marital_statuses' => collect(MaritalStatus::cases())->map(fn ($e) => ['value' => $e->value, 'label' => $e->label()]),
            'employment_types' => collect(EmploymentType::cases())->map(fn ($e) => ['value' => $e->value, 'label' => $e->label()]),
            'active_statuses' => collect(ActiveStatus::cases())->map(fn ($e) => ['value' => $e->value, 'label' => $e->label()]),
            'document_types' => collect(DocumentType::cases())->map(fn ($e) => ['value' => $e->value, 'label' => $e->label()]),
        ];
    }
}
