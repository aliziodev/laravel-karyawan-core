<?php

namespace Aliziodev\LaravelKaryawanCore\Tests;

use Aliziodev\LaravelKaryawanCore\Providers\KaryawanServiceProvider;
use Aliziodev\LaravelKaryawanCore\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            KaryawanServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('karyawan.employee_code.prefix', 'EMP');
        $app['config']->set('karyawan.employee_code.pad_length', 5);
        $app['config']->set('karyawan.employee_code.auto_generate', true);
        $app['config']->set('karyawan.user_model', User::class);

        // Aktifkan route untuk controller tests (tanpa auth, tapi tetap butuh SubstituteBindings untuk route model binding)
        $app['config']->set('karyawan.routes.api.enabled', true);
        $app['config']->set('karyawan.routes.api.middleware', ['Illuminate\Routing\Middleware\SubstituteBindings']);
        $app['config']->set('karyawan.routes.web.enabled', false);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            $modelBaseName = class_basename($modelName);

            return 'Aliziodev\\LaravelKaryawanCore\\Database\\Factories\\'.$modelBaseName.'Factory';
        });
    }
}
