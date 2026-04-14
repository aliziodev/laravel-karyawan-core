<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade;

use Aliziodev\LaravelKaryawanCore\Http\Requests\Department\CreateDepartmentRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Department\UpdateDepartmentRequest;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Aliziodev\LaravelKaryawanCore\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $departments = Department::query()
            ->with('company')
            ->when($request->filled('company_id'), fn ($q) => $q->byCompany((int) $request->company_id))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->paginate(20)
            ->withQueryString();

        return view('karyawan.department.index', [
            'departments' => $departments,
            'filters' => $request->only(['company_id', 'active_only']),
            'companies' => Company::active()->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('karyawan.department.create', [
            'companies' => Company::active()->get(['id', 'name']),
        ]);
    }

    public function store(CreateDepartmentRequest $request): RedirectResponse
    {
        $department = Department::create($request->validated());

        return redirect()
            ->route('karyawan.departments.show', $department)
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function show(Department $department): View
    {
        return view('karyawan.department.show', [
            'department' => $department->load('company'),
        ]);
    }

    public function edit(Department $department): View
    {
        return view('karyawan.department.edit', [
            'department' => $department->load('company'),
            'companies' => Company::active()->get(['id', 'name']),
        ]);
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()
            ->route('karyawan.departments.show', $department)
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return redirect()
            ->route('karyawan.departments.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}
