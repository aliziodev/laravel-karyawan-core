<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\Employee;

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class EmployeeHistoryController extends Controller
{
    public function __invoke(Employee $employee): View
    {
        return view('karyawan.employee.history.index', [
            'employee' => $employee,
            'histories' => $employee->histories()->latest()->get(),
        ]);
    }
}
