<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Inertia\Employee;

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeHistoryController extends Controller
{
    public function __invoke(Employee $employee): Response
    {
        return Inertia::render('karyawan/employee/history/index', [
            'employee' => $employee,
            'histories' => $employee->histories()->latest()->get(),
        ]);
    }
}
