<?php

use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Blade\Employee\ExportEmployeesController as BladeExportEmployeesController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Web\Inertia\Employee\ExportEmployeesController as InertiaExportEmployeesController;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Tests\Models\User;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;

it('web inertia export controller returns downloadable xlsx with filters', function () {
    config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);

    Route::middleware(['web', 'auth'])
        ->get('/test-web-inertia/employees/export', InertiaExportEmployeesController::class);

    $user = User::query()->create([
        'name' => 'Inertia User',
        'email' => 'inertia@example.test',
        'password' => 'secret',
    ]);

    Employee::factory()->create([
        'full_name' => 'Inertia Cocok',
        'active_status' => 'active',
        'join_date' => '2026-02-10',
    ]);

    Employee::factory()->create([
        'full_name' => 'Inertia Tidak Cocok',
        'active_status' => 'inactive',
        'join_date' => '2026-02-10',
    ]);

    $response = $this->actingAs($user)->get('/test-web-inertia/employees/export?active_status=active&join_date_from=2026-01-01&join_date_to=2026-12-31');

    $response->assertOk();
    $response->assertDownload();
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    $file = $response->baseResponse->getFile()->getPathname();
    $sheet = IOFactory::load($file)->getActiveSheet();

    expect($sheet->getCell('B2')->getValue())->toBe('Inertia Cocok');
    expect($sheet->getCell('B3')->getValue())->toBeNull();
});

it('web blade export controller returns downloadable xlsx with filters', function () {
    config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);

    Route::middleware(['web', 'auth'])
        ->get('/test-web-blade/employees/export', BladeExportEmployeesController::class);

    $user = User::query()->create([
        'name' => 'Blade User',
        'email' => 'blade@example.test',
        'password' => 'secret',
    ]);

    Employee::factory()->create([
        'full_name' => 'Blade Cocok',
        'active_status' => 'active',
        'join_date' => '2026-03-15',
    ]);

    Employee::factory()->create([
        'full_name' => 'Blade Tidak Cocok',
        'active_status' => 'active',
        'join_date' => '2024-03-15',
    ]);

    $response = $this->actingAs($user)->get('/test-web-blade/employees/export?active_status=active&join_date_from=2026-01-01&join_date_to=2026-12-31');

    $response->assertOk();
    $response->assertDownload();
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    $file = $response->baseResponse->getFile()->getPathname();
    $sheet = IOFactory::load($file)->getActiveSheet();

    expect($sheet->getCell('B2')->getValue())->toBe('Blade Cocok');
    expect($sheet->getCell('B3')->getValue())->toBeNull();
});
