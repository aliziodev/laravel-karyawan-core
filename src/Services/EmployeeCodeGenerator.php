<?php

namespace Aliziodev\LaravelKaryawanCore\Services;

use Aliziodev\LaravelKaryawanCore\Contracts\EmployeeCodeGeneratorContract;
use Aliziodev\LaravelKaryawanCore\Exceptions\EmployeeCodeGenerationException;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeCodeGenerator implements EmployeeCodeGeneratorContract
{
    public function generate(?string $prefix = null): string
    {
        $prefix = $prefix ?? config('karyawan.employee_code.prefix', 'EMP');
        $padLength = (int) config('karyawan.employee_code.pad_length', 5);

        return DB::transaction(function () use ($prefix, $padLength) {
            // Pessimistic lock: kunci semua baris yang cocok agar tidak ada dua proses
            // yang generate kode yang sama secara bersamaan.
            $count = Employee::withTrashed()
                ->where('employee_code', 'like', $prefix.'%')
                ->lockForUpdate()
                ->count();

            $next = $count + 1;
            $code = $prefix.str_pad((string) $next, $padLength, '0', STR_PAD_LEFT);

            // Pastikan kode yang di-generate benar-benar belum dipakai
            // (antisipasi jika ada gap dari soft-delete)
            while (Employee::withTrashed()->where('employee_code', $code)->exists()) {
                $next++;
                $code = $prefix.str_pad((string) $next, $padLength, '0', STR_PAD_LEFT);
            }

            if (strlen($code) > 50) {
                throw EmployeeCodeGenerationException::failedToGenerate($prefix);
            }

            return $code;
        });
    }
}
