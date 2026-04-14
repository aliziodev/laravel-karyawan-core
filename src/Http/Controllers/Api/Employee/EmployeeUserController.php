<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee;

use Aliziodev\LaravelKaryawanCore\Actions\LinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Actions\UnlinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Employee\LinkEmployeeUserRequest;
use Aliziodev\LaravelKaryawanCore\Http\Resources\EmployeeResource;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EmployeeUserController extends Controller
{
    public function __construct(
        private readonly LinkEmployeeUserAction $linkUserAction,
        private readonly UnlinkEmployeeUserAction $unlinkUserAction,
    ) {}

    public function store(LinkEmployeeUserRequest $request, Employee $employee): EmployeeResource
    {
        $employee = $this->linkUserAction->execute(
            employee: $employee,
            userId: (int) $request->user_id,
            createdBy: $request->user()?->id,
        );

        return new EmployeeResource($employee);
    }

    public function destroy(Request $request, Employee $employee): JsonResponse
    {
        $this->unlinkUserAction->execute(
            employee: $employee,
            createdBy: $request->user()?->id,
        );

        return response()->json(['message' => 'Akun login berhasil dilepas.']);
    }
}
