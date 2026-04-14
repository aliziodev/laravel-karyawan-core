<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee;

use Aliziodev\LaravelKaryawanCore\Actions\ChangeEmployeeStatusAction;
use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Employee\ChangeEmployeeStatusRequest;
use Aliziodev\LaravelKaryawanCore\Http\Resources\EmployeeResource;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Routing\Controller;

class EmployeeStatusController extends Controller
{
    public function __construct(
        private readonly ChangeEmployeeStatusAction $changeStatusAction,
    ) {}

    public function __invoke(ChangeEmployeeStatusRequest $request, Employee $employee): EmployeeResource
    {
        $employee = $this->changeStatusAction->execute(
            employee: $employee,
            newStatus: ActiveStatus::from($request->active_status),
            effectiveDate: $request->effective_date,
            notes: $request->notes,
            createdBy: $request->user()?->id,
        );

        return new EmployeeResource($employee);
    }
}
