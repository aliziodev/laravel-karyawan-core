<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api;

use Aliziodev\LaravelKaryawanCore\Http\Requests\Branch\CreateBranchRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Branch\UpdateBranchRequest;
use Aliziodev\LaravelKaryawanCore\Http\Resources\BranchResource;
use Aliziodev\LaravelKaryawanCore\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class BranchController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $branches = Branch::query()
            ->with('company')
            ->when($request->filled('company_id'), fn ($q) => $q->byCompany((int) $request->company_id))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->paginate($request->integer('per_page', 20));

        return BranchResource::collection($branches);
    }

    public function store(CreateBranchRequest $request): JsonResponse
    {
        $branch = Branch::create($request->validated());

        return (new BranchResource($branch->load('company')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Branch $branch): BranchResource
    {
        return new BranchResource($branch->load('company'));
    }

    public function update(UpdateBranchRequest $request, Branch $branch): BranchResource
    {
        $branch->update($request->validated());

        return new BranchResource($branch->fresh('company'));
    }

    public function destroy(Branch $branch): JsonResponse
    {
        $branch->delete();

        return response()->json(['message' => 'Cabang berhasil dihapus.']);
    }
}
