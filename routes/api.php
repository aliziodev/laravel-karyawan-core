<?php

use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\BranchController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\CompanyController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\DepartmentController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee\EmployeeDocumentController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee\EmployeeEmergencyContactController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee\EmployeeHistoryController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee\EmployeeStatusController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee\EmployeeUserController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\EmployeeController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\PositionController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('karyawan.routes.api.prefix', 'api/karyawan'),
    'middleware' => config('karyawan.routes.api.middleware', ['api', 'auth:sanctum']),
], function () {

    // --- Master Data Organisasi ---
    Route::apiResource('companies', CompanyController::class)
        ->names('karyawan.api.companies');

    Route::apiResource('branches', BranchController::class)
        ->names('karyawan.api.branches');

    Route::apiResource('departments', DepartmentController::class)
        ->names('karyawan.api.departments');

    Route::apiResource('positions', PositionController::class)
        ->names('karyawan.api.positions');

    // --- Employee CRUD ---
    Route::apiResource('employees', EmployeeController::class)
        ->names('karyawan.api.employees');

    // --- Employee Sub-Resources ---
    Route::prefix('employees/{employee}')->name('karyawan.api.employees.')->group(function () {

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
        Route::get('emergency-contacts', [EmployeeEmergencyContactController::class, 'index'])
            ->name('emergency-contacts.index');
        Route::post('emergency-contacts', [EmployeeEmergencyContactController::class, 'store'])
            ->name('emergency-contacts.store');
        Route::put('emergency-contacts/{contact}', [EmployeeEmergencyContactController::class, 'update'])
            ->name('emergency-contacts.update');
        Route::delete('emergency-contacts/{contact}', [EmployeeEmergencyContactController::class, 'destroy'])
            ->name('emergency-contacts.destroy');

        // History
        Route::get('histories', EmployeeHistoryController::class)
            ->name('histories.index');
    });
});
