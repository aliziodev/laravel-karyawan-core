<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade;

use Aliziodev\LaravelKaryawanCore\Http\Requests\Company\CreateCompanyRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Company\UpdateCompanyRequest;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $companies = Company::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->paginate(20)
            ->withQueryString();

        return view('karyawan.company.index', [
            'companies' => $companies,
            'filters' => $request->only(['search', 'active_only']),
        ]);
    }

    public function create(): View
    {
        return view('karyawan.company.create');
    }

    public function store(CreateCompanyRequest $request): RedirectResponse
    {
        $company = Company::create($request->validated());

        return redirect()
            ->route('karyawan.companies.show', $company)
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function show(Company $company): View
    {
        return view('karyawan.company.show', [
            'company' => $company->load(['branches', 'departments', 'positions']),
        ]);
    }

    public function edit(Company $company): View
    {
        return view('karyawan.company.edit', ['company' => $company]);
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
