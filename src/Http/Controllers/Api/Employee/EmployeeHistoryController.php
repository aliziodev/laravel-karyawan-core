<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee;

use Aliziodev\LaravelKaryawanCore\Http\Resources\EmployeeHistoryResource;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class EmployeeHistoryController extends Controller
{
    public function __invoke(Employee $employee): AnonymousResourceCollection
    {
        return EmployeeHistoryResource::collection(
            $employee->histories()->latest()->get()
        );
    }
}
