<?php

namespace Aliziodev\LaravelKaryawanCore\Actions;

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Database\Eloquent\Builder;

class BuildEmployeeExportQueryAction
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function execute(array $filters): Builder
    {
        $sortBy = $filters['sort_by'] ?? 'full_name';
        $sortDirection = $filters['sort_direction'] ?? 'asc';

        return Employee::query()
            ->with(['company', 'branch', 'department', 'position'])
            ->when(isset($filters['search']), fn (Builder $q) => $q->search((string) $filters['search']))
            ->when(isset($filters['company_id']), fn (Builder $q) => $q->byCompany((int) $filters['company_id']))
            ->when(isset($filters['branch_id']), fn (Builder $q) => $q->byBranch((int) $filters['branch_id']))
            ->when(isset($filters['department_id']), fn (Builder $q) => $q->byDepartment((int) $filters['department_id']))
            ->when(isset($filters['position_id']), fn (Builder $q) => $q->byPosition((int) $filters['position_id']))
            ->when(isset($filters['active_status']), fn (Builder $q) => $q->where('active_status', (string) $filters['active_status']))
            ->when(isset($filters['employment_type']), fn (Builder $q) => $q->where('employment_type', (string) $filters['employment_type']))
            ->when(($filters['with_login'] ?? false) === true, fn (Builder $q) => $q->withLogin())
            ->when(($filters['without_login'] ?? false) === true, fn (Builder $q) => $q->withoutLogin())
            ->when(isset($filters['join_date_from']), fn (Builder $q) => $q->whereDate('join_date', '>=', (string) $filters['join_date_from']))
            ->when(isset($filters['join_date_to']), fn (Builder $q) => $q->whereDate('join_date', '<=', (string) $filters['join_date_to']))
            ->when(isset($filters['exit_date_from']), fn (Builder $q) => $q->whereDate('exit_date', '>=', (string) $filters['exit_date_from']))
            ->when(isset($filters['exit_date_to']), fn (Builder $q) => $q->whereDate('exit_date', '<=', (string) $filters['exit_date_to']))
            ->when(isset($filters['created_at_from']), fn (Builder $q) => $q->whereDate('created_at', '>=', (string) $filters['created_at_from']))
            ->when(isset($filters['created_at_to']), fn (Builder $q) => $q->whereDate('created_at', '<=', (string) $filters['created_at_to']))
            ->orderBy($sortBy, $sortDirection);
    }
}
