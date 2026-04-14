<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api;

use Aliziodev\LaravelKaryawanCore\Actions\CreateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\Actions\UpdateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Employee\CreateEmployeeRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Employee\UpdateEmployeeRequest;
use Aliziodev\LaravelKaryawanCore\Http\Resources\EmployeeResource;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly CreateEmployeeAction $createAction,
        private readonly UpdateEmployeeAction $updateAction,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $employees = $this->queryEmployees($request)
            ->paginate($request->integer('per_page', 20));

        return EmployeeResource::collection($employees);
    }

    public function store(CreateEmployeeRequest $request): JsonResponse
    {
        $employee = $this->createAction->execute(EmployeeData::fromRequest($request));

        return (new EmployeeResource($employee->load(['company', 'branch', 'department', 'position'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Employee $employee): EmployeeResource
    {
        return new EmployeeResource(
            $employee->load(['company', 'branch', 'department', 'position', 'manager', 'documents', 'emergencyContacts'])
        );
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): EmployeeResource
    {
        $employee = $this->updateAction->execute($employee, EmployeeData::fromRequest($request));

        return new EmployeeResource($employee->load(['company', 'branch', 'department', 'position']));
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $employee->delete();

        return response()->json(['message' => 'Karyawan berhasil dihapus.']);
    }

    private function queryEmployees(Request $request): Builder
    {
        return Employee::query()
            ->with(['company', 'branch', 'department', 'position'])
            ->when($request->filled('search'), fn ($q) => $q->search($request->string('search')->toString()))
            ->when($request->filled('company_id'), fn ($q) => $q->byCompany((int) $request->company_id))
            ->when($request->filled('branch_id'), fn ($q) => $q->byBranch((int) $request->branch_id))
            ->when($request->filled('department_id'), fn ($q) => $q->byDepartment((int) $request->department_id))
            ->when($request->filled('position_id'), fn ($q) => $q->byPosition((int) $request->position_id))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->when($request->boolean('with_login'), fn ($q) => $q->withLogin())
            ->when($request->boolean('without_login'), fn ($q) => $q->withoutLogin());
    }
}
