<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api;

use Aliziodev\LaravelKaryawanCore\Http\Requests\Position\CreatePositionRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Position\UpdatePositionRequest;
use Aliziodev\LaravelKaryawanCore\Http\Resources\PositionResource;
use Aliziodev\LaravelKaryawanCore\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class PositionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $positions = Position::query()
            ->with('company')
            ->when($request->filled('company_id'), fn ($q) => $q->byCompany((int) $request->company_id))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->paginate($request->integer('per_page', 20));

        return PositionResource::collection($positions);
    }

    public function store(CreatePositionRequest $request): JsonResponse
    {
        $position = Position::create($request->validated());

        return (new PositionResource($position->load('company')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Position $position): PositionResource
    {
        return new PositionResource($position->load('company'));
    }

    public function update(UpdatePositionRequest $request, Position $position): PositionResource
    {
        $position->update($request->validated());

        return new PositionResource($position->fresh('company'));
    }

    public function destroy(Position $position): JsonResponse
    {
        $position->delete();

        return response()->json(['message' => 'Jabatan berhasil dihapus.']);
    }
}
