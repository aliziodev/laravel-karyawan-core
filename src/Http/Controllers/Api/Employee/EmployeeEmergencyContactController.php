<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee;

use Aliziodev\LaravelKaryawanCore\Http\Requests\EmergencyContact\StoreEmergencyContactRequest;
use Aliziodev\LaravelKaryawanCore\Http\Requests\EmergencyContact\UpdateEmergencyContactRequest;
use Aliziodev\LaravelKaryawanCore\Http\Resources\EmployeeEmergencyContactResource;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeEmergencyContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class EmployeeEmergencyContactController extends Controller
{
    public function index(Employee $employee): AnonymousResourceCollection
    {
        return EmployeeEmergencyContactResource::collection(
            $employee->emergencyContacts()->orderByDesc('is_primary')->get()
        );
    }

    public function store(StoreEmergencyContactRequest $request, Employee $employee): JsonResponse
    {
        $contact = $employee->emergencyContacts()->create($request->validated());

        return (new EmployeeEmergencyContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateEmergencyContactRequest $request, Employee $employee, EmployeeEmergencyContact $contact): EmployeeEmergencyContactResource
    {
        abort_unless($contact->employee_id === $employee->id, 404);

        $contact->update($request->validated());

        return new EmployeeEmergencyContactResource($contact->fresh());
    }

    public function destroy(Employee $employee, EmployeeEmergencyContact $contact): JsonResponse
    {
        abort_unless($contact->employee_id === $employee->id, 404);

        $contact->delete();

        return response()->json(['message' => 'Kontak darurat berhasil dihapus.']);
    }
}
