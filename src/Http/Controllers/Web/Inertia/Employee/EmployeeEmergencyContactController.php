<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Inertia\Employee;

use Aliziodev\LaravelKaryawanCore\Http\Requests\EmergencyContact\StoreEmergencyContactRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\EmergencyContact\UpdateEmergencyContactRequest;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeEmergencyContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeEmergencyContactController extends Controller
{
    public function index(Employee $employee): Response
    {
        return Inertia::render('karyawan/employee/emergency-contact/index', [
            'employee' => $employee,
            'contacts' => $employee->emergencyContacts()->orderByDesc('is_primary')->get(),
        ]);
    }

    public function create(Employee $employee): Response
    {
        return Inertia::render('karyawan/employee/emergency-contact/create', [
            'employee' => $employee,
        ]);
    }

    public function store(StoreEmergencyContactRequest $request, Employee $employee): RedirectResponse
    {
        $employee->emergencyContacts()->create($request->validated());

        return redirect()
            ->route('karyawan.employees.emergency-contacts.index', $employee)
            ->with('success', 'Kontak darurat berhasil ditambahkan.');
    }

    public function edit(Employee $employee, EmployeeEmergencyContact $contact): Response
    {
        abort_unless($contact->employee_id === $employee->id, 404);

        return Inertia::render('karyawan/employee/emergency-contact/edit', [
            'employee' => $employee,
            'contact' => $contact,
        ]);
    }

    public function update(UpdateEmergencyContactRequest $request, Employee $employee, EmployeeEmergencyContact $contact): RedirectResponse
    {
        abort_unless($contact->employee_id === $employee->id, 404);

        $contact->update($request->validated());

        return redirect()
            ->route('karyawan.employees.emergency-contacts.index', $employee)
            ->with('success', 'Kontak darurat berhasil diperbarui.');
    }

    public function destroy(Employee $employee, EmployeeEmergencyContact $contact): RedirectResponse
    {
        abort_unless($contact->employee_id === $employee->id, 404);

        $contact->delete();

        return back()->with('success', 'Kontak darurat berhasil dihapus.');
    }
}
