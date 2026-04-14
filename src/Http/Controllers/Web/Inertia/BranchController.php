<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Inertia;

use Aliziodev\LaravelKaryawanCore\Http\Requests\Branch\CreateBranchRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Branch\UpdateBranchRequest;
use Aliziodev\LaravelKaryawanCore\Models\Branch;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    public function index(Request $request): Response
    {
        $branches = Branch::query()
            ->with('company')
            ->when($request->filled('company_id'), fn ($q) => $q->byCompany((int) $request->company_id))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('karyawan/branch/index', [
            'branches' => $branches,
            'filters' => $request->only(['company_id', 'active_only']),
            'companies' => Company::active()->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('karyawan/branch/create', [
            'companies' => Company::active()->get(['id', 'name']),
        ]);
    }

    public function store(CreateBranchRequest $request): RedirectResponse
    {
        $branch = Branch::create($request->validated());

        return redirect()
            ->route('karyawan.branches.show', $branch)
            ->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function show(Branch $branch): Response
    {
        return Inertia::render('karyawan/branch/show', [
            'branch' => $branch->load('company'),
        ]);
    }

    public function edit(Branch $branch): Response
    {
        return Inertia::render('karyawan/branch/edit', [
            'branch' => $branch->load('company'),
            'companies' => Company::active()->get(['id', 'name']),
        ]);
    }

    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $branch->update($request->validated());

        return redirect()
            ->route('karyawan.branches.show', $branch)
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $branch->delete();

        return redirect()
            ->route('karyawan.branches.index')
            ->with('success', 'Cabang berhasil dihapus.');
    }
}
