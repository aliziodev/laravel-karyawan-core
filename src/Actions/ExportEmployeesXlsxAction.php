<?php

namespace Aliziodev\LaravelKaryawanCore\Actions;

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportEmployeesXlsxAction
{
    /**
     * @param  iterable<Employee>  $employees
     */
    public function execute(iterable $employees): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Employees');

        $sheet->fromArray([
            'Employee Code',
            'Full Name',
            'Work Email',
            'Phone',
            'Company',
            'Branch',
            'Department',
            'Position',
            'Status',
            'Join Date',
        ], null, 'A1');

        $row = 2;

        foreach ($employees as $employee) {
            $sheet->fromArray([
                $employee->employee_code,
                $employee->full_name,
                $employee->work_email,
                $employee->phone,
                $employee->company?->name,
                $employee->branch?->name,
                $employee->department?->name,
                $employee->position?->name,
                $employee->active_status?->value ?? (string) $employee->active_status,
                $employee->join_date?->toDateString(),
            ], null, 'A'.$row);

            $row++;
        }

        $path = tempnam(sys_get_temp_dir(), 'karyawan_employees_');

        if ($path === false) {
            throw new \RuntimeException('Gagal membuat file temporary untuk export employee.');
        }

        $xlsxPath = $path.'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($xlsxPath);

        return $xlsxPath;
    }
}
