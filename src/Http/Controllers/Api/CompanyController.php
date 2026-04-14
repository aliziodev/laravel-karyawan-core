<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api;

use Aliziodev\LaravelKaryawanCore\Http\Requests\Company\CreateCompanyRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Company\UpdateCompanyRequest;
use Aliziodev\LaravelKaryawanCore\Http\Resources\CompanyResource;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class CompanyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $companies = Company::query()
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->paginate($request->integer('per_page', 20));

        return CompanyResource::collection($companies);
    }

    public function store(CreateCompanyRequest $request): JsonResponse
    {
        $company = Company::create($request->validated());

        return (new CompanyResource($company))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Company $company): CompanyResource
    {
        return new CompanyResource($company->load(['branches', 'departments', 'positions']));
    }

    public function update(UpdateCompanyRequest $request, Company $company): CompanyResource
    {
        $company->update($request->validated());

        return new CompanyResource($company->fresh());
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json(['message' => 'Perusahaan berhasil dihapus.']);
    }
}
