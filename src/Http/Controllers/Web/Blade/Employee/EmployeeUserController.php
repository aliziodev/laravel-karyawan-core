<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\Employee;

use Aliziodev\LaravelKaryawanCore\Actions\LinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Actions\UnlinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Employee\LinkEmployeeUserRequest;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EmployeeUserController extends Controller
{
    public function __construct(
        private readonly LinkEmployeeUserAction $linkUserAction,
        private readonly UnlinkEmployeeUserAction $unlinkUserAction,
    ) {}

    public function store(LinkEmployeeUserRequest $request, Employee $employee): RedirectResponse
    {
        $this->linkUserAction->execute(
            employee: $employee,
            userId: (int) $request->user_id,
            createdBy: $request->user()?->id,
        );

        return back()->with('success', 'Akun login berhasil dikaitkan.');
    }

    public function destroy(Request $request, Employee $employee): RedirectResponse
    {
        $this->unlinkUserAction->execute(
            employee: $employee,
            createdBy: $request->user()?->id,
        );

        return back()->with('success', 'Akun login berhasil dilepas.');
    }
}
