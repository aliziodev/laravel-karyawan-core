<?php

use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\BranchController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\CompanyController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\DepartmentController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\Employee\EmployeeDocumentController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\Employee\EmployeeEmergencyContactController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\Employee\ExportEmployeesController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\Employee\EmployeeHistoryController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\Employee\EmployeeStatusController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\Employee\EmployeeUserController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\EmployeeController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\PositionController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('karyawan.routes.web.prefix', 'karyawan'),
    'middleware' => config('karyawan.routes.web.middleware', ['web', 'auth']),
    'as' => 'karyawan.',
], function () {

    // --- Master Data Organisasi ---
    Route::resource('companies', CompanyController::class)
        ->names('companies');

    Route::resource('branches', BranchController::class)
        ->names('branches');

    Route::resource('departments', DepartmentController::class)
        ->names('departments');

    Route::resource('positions', PositionController::class)
        ->names('positions');

    // --- Employee CRUD ---
    Route::get('employees/export', ExportEmployeesController::class)
        ->name('employees.export');

    Route::resource('employees', EmployeeController::class)
        ->names('employees');

    // --- Employee Sub-Resources ---
    Route::prefix('employees/{employee}')->name('employees.')->group(function () {

        // Status
        Route::patch('status', EmployeeStatusController::class)
            ->name('status');

        // User link/unlink
        Route::post('user', [EmployeeUserController::class, 'store'])
            ->name('user.store');
        Route::delete('user', [EmployeeUserController::class, 'destroy'])
            ->name('user.destroy');

        // Documents
        Route::get('documents', [EmployeeDocumentController::class, 'index'])
            ->name('documents.index');
        Route::post('documents', [EmployeeDocumentController::class, 'store'])
            ->name('documents.store');
        Route::delete('documents/{document}', [EmployeeDocumentController::class, 'destroy'])
            ->name('documents.destroy');

        // Emergency Contacts
        Route::resource('emergency-contacts', EmployeeEmergencyContactController::class)
            ->names('emergency-contacts')
            ->shallow();

        // History
        Route::get('histories', EmployeeHistoryController::class)
            ->name('histories.index');
    });
});
