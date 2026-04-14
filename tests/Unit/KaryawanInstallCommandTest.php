<?php

use Aliziodev\LaravelKaryawanCore\Console\Commands\KaryawanInstallCommand;
use Illuminate\Filesystem\Filesystem;

function invokePrivateMethod(object $object, string $method, array $args = []): mixed
{
    $reflection = new ReflectionClass($object);
    $privateMethod = $reflection->getMethod($method);
    $privateMethod->setAccessible(true);

    return $privateMethod->invokeArgs($object, $args);
}

it('normalizes published controller namespaces to App namespace', function () {
    $filesystem = app(Filesystem::class);
    $controllerRoot = app_path('Http/Controllers/Karyawan/Api');
    $controllerFile = $controllerRoot.'/CompanyController.php';

    $filesystem->ensureDirectoryExists($controllerRoot);

    file_put_contents($controllerFile, <<<'PHP'
<?php

namespace Aliziodev\LaravelKaryawanCore\Http\Controllers\Api;

use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\EmployeeController;

class CompanyController extends EmployeeController {}
PHP
    );

    $command = new KaryawanInstallCommand;
    invokePrivateMethod($command, 'normalizePublishedControllerNamespaces');

    $updated = file_get_contents($controllerFile);

    expect($updated)->toContain('namespace App\\Http\\Controllers\\Karyawan\\Api;');
    expect($updated)->toContain('use App\\Http\\Controllers\\Karyawan\\Api\\EmployeeController;');

    $filesystem->deleteDirectory(app_path('Http/Controllers/Karyawan'));
});

it('normalizes published route file controller imports to App namespace', function () {
    $filesystem = app(Filesystem::class);
    $routeDirectory = base_path('routes/karyawan');
    $routeFile = $routeDirectory.'/api.php';

    $filesystem->ensureDirectoryExists($routeDirectory);

    file_put_contents($routeFile, <<<'PHP'
<?php

use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\CompanyController;
use Aliziodev\LaravelKaryawanCore\Http\Controllers\Api\Employee\ExportEmployeesController;
PHP
    );

    $command = new KaryawanInstallCommand;
    invokePrivateMethod($command, 'normalizePublishedRouteFile', [$routeFile]);

    $updated = file_get_contents($routeFile);

    expect($updated)->toContain('use App\\Http\\Controllers\\Karyawan\\Api\\CompanyController;');
    expect($updated)->toContain('use App\\Http\\Controllers\\Karyawan\\Api\\Employee\\ExportEmployeesController;');

    $filesystem->deleteDirectory($routeDirectory);
});

it('ensures route include block is appended once', function () {
    $filesystem = app(Filesystem::class);
    $routeDirectory = base_path('routes');
    $targetRoute = $routeDirectory.'/api.php';
    $includeBlock = "if (file_exists(base_path('routes/karyawan/api.php'))) {\n    require base_path('routes/karyawan/api.php');\n}";

    $filesystem->ensureDirectoryExists($routeDirectory);

    file_put_contents($targetRoute, "<?php\n\nRoute::get('/health', fn () => 'ok');\n");

    $command = new KaryawanInstallCommand;
    invokePrivateMethod($command, 'ensureRouteInclude', [$targetRoute, $includeBlock]);
    invokePrivateMethod($command, 'ensureRouteInclude', [$targetRoute, $includeBlock]);

    $updated = file_get_contents($targetRoute);

    expect(substr_count($updated, "require base_path('routes/karyawan/api.php');"))->toBe(1);

    $filesystem->delete($targetRoute);
});
