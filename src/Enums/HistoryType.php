<?php

namespace Aliziodev\LaravelKaryawanCore\Enums;

enum HistoryType: string
{
    case PositionChange = 'position_change';
    case DepartmentChange = 'department_change';
    case BranchChange = 'branch_change';
    case CompanyChange = 'company_change';
    case StatusChange = 'status_change';
    case EmploymentTypeChange = 'employment_type_change';
    case ManagerChange = 'manager_change';
    case UserLinked = 'user_linked';
    case UserUnlinked = 'user_unlinked';

    public function label(): string
    {
        return match ($this) {
            self::PositionChange => 'Perubahan Jabatan',
            self::DepartmentChange => 'Perubahan Departemen',
            self::BranchChange => 'Perubahan Cabang',
            self::CompanyChange => 'Perubahan Perusahaan',
            self::StatusChange => 'Perubahan Status',
            self::EmploymentTypeChange => 'Perubahan Jenis Hubungan Kerja',
            self::ManagerChange => 'Perubahan Atasan',
            self::UserLinked => 'Akun Login Dikaitkan',
            self::UserUnlinked => 'Akun Login Dilepas',
        };
    }
}
