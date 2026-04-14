<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Inertia;

use Aliziodev\LaravelKaryawanCore\Http\Requests\Company\CreateCompanyRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Company\UpdateCompanyRequest;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(Request $request): Response
    {
        $companies = Company::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('karyawan/company/index', [
            'companies' => $companies,
            'filters' => $request->only(['search', 'active_only']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('karyawan/company/create');
    }

    public function store(CreateCompanyRequest $request): RedirectResponse
    {
        $company = Company::create($request->validated());

        return redirect()
            ->route('karyawan.companies.show', $company)
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function show(Company $company): Response
    {
        return Inertia::render('karyawan/company/show', [
            'company' => $company->load(['branches', 'departments', 'positions']),
        ]);
    }

    public function edit(Company $company): Response
    {
        return Inertia::render('karyawan/company/edit', ['company' => $company]);
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $company->update($request->validated());

        return redirect()
            ->route('karyawan.companies.show', $company)
            ->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()
            ->route('karyawan.companies.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
    }
}
