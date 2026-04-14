<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Inertia;

use Aliziodev\LaravelKaryawanCore\Http\Requests\Position\CreatePositionRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Position\UpdatePositionRequest;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Aliziodev\LaravelKaryawanCore\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PositionController extends Controller
{
    public function index(Request $request): Response
    {
        $positions = Position::query()
            ->with('company')
            ->when($request->filled('company_id'), fn ($q) => $q->byCompany((int) $request->company_id))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('karyawan/position/index', [
            'positions' => $positions,
            'filters' => $request->only(['company_id', 'active_only']),
            'companies' => Company::active()->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('karyawan/position/create', [
            'companies' => Company::active()->get(['id', 'name']),
        ]);
    }

    public function store(CreatePositionRequest $request): RedirectResponse
    {
        $position = Position::create($request->validated());

        return redirect()
            ->route('karyawan.positions.show', $position)
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function show(Position $position): Response
    {
        return Inertia::render('karyawan/position/show', [
            'position' => $position->load('company'),
        ]);
    }

    public function edit(Position $position): Response
    {
        return Inertia::render('karyawan/position/edit', [
            'position' => $position->load('company'),
            'companies' => Company::active()->get(['id', 'name']),
        ]);
    }

    public function update(UpdatePositionRequest $request, Position $position): RedirectResponse
    {
        $position->update($request->validated());

        return redirect()
            ->route('karyawan.positions.show', $position)
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()
            ->route('karyawan.positions.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
