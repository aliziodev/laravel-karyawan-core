<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api;

use Aliziodev\LaravelKaryawanCore\Http\Requests\Department\CreateDepartmentRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Department\UpdateDepartmentRequest;
use Aliziodev\LaravelKaryawanCore\Http\Resources\DepartmentResource;
use Aliziodev\LaravelKaryawanCore\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class DepartmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $departments = Department::query()
            ->with('company')
            ->when($request->filled('company_id'), fn ($q) => $q->byCompany((int) $request->company_id))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->paginate($request->integer('per_page', 20));

        return DepartmentResource::collection($departments);
    }

    public function store(CreateDepartmentRequest $request): JsonResponse
    {
        $department = Department::create($request->validated());

        return (new DepartmentResource($department->load('company')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Department $department): DepartmentResource
    {
        return new DepartmentResource($department->load('company'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): DepartmentResource
    {
        $department->update($request->validated());

        return new DepartmentResource($department->fresh('company'));
    }

    public function destroy(Department $department): JsonResponse
    {
        $department->delete();

        return response()->json(['message' => 'Departemen berhasil dihapus.']);
    }
}
