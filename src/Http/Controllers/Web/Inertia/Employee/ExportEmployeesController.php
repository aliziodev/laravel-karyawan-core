<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Inertia\Employee;

use Aliziodev\LaravelKaryawanCore\Actions\BuildEmployeeExportQueryAction;
use Aliziodev\LaravelKaryawanCore\Actions\ExportEmployeesXlsxAction;
use Aliziodev\LaravelKaryawanCore\Http\Requests\Employee\WebInertiaExportEmployeesRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportEmployeesController extends Controller
{
    public function __invoke(
        WebInertiaExportEmployeesRequest $request,
        BuildEmployeeExportQueryAction $queryAction,
        ExportEmployeesXlsxAction $exportAction,
    ): BinaryFileResponse {
        $employees = $queryAction->execute($request->filters())->get();
        $filePath = $exportAction->execute($employees);

        return response()->download(
            $filePath,
            'employees-'.now()->format('Ymd_His').'.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }
}
