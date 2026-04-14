<?php

namespace Aliziodev\LaravelKaryawanCore\Providers;

use Aliziodev\LaravelKaryawanCore\Actions\ChangeEmployeeStatusAction;
use Aliziodev\LaravelKaryawanCore\Actions\CreateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\Actions\DeleteEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\Actions\LinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Actions\StoreEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\Actions\UnlinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Actions\UpdateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\Console\Commands\KaryawanInstallCommand;
use Aliziodev\LaravelKaryawanCore\Contracts\EmployeeCodeGeneratorContract;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;
use Aliziodev\LaravelKaryawanCore\Policies\EmployeeDocumentPolicy;
use Aliziodev\LaravelKaryawanCore\Policies\EmployeePolicy;
use Aliziodev\LaravelKaryawanCore\Services\EmployeeCodeGenerator;
use Aliziodev\LaravelKaryawanCore\Services\EmployeeDocumentService;
use Aliziodev\LaravelKaryawanCore\Services\EmployeeService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class KaryawanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/karyawan.php',
            'karyawan'
        );

        $this->registerServices();
        $this->registerActions();
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        $this->registerPolicies();
        $this->registerRoutes();
        $this->registerPublishing();
        $this->registerCommands();
    }

    private function registerServices(): void
    {
        $this->app->bind(
            EmployeeCodeGeneratorContract::class,
            EmployeeCodeGenerator::class
        );

        $this->app->singleton(EmployeeDocumentService::class);

        $this->app->singleton(EmployeeService::class, function ($app) {
            return new EmployeeService(
                createAction: $app->make(CreateEmployeeAction::class),
                updateAction: $app->make(UpdateEmployeeAction::class),
                linkUserAction: $app->make(LinkEmployeeUserAction::class),
                unlinkUserAction: $app->make(UnlinkEmployeeUserAction::class),
                changeStatusAction: $app->make(ChangeEmployeeStatusAction::class),
                storeDocumentAction: $app->make(StoreEmployeeDocumentAction::class),
                deleteDocumentAction: $app->make(DeleteEmployeeDocumentAction::class),
            );
        });
    }

    private function registerActions(): void
    {
        $this->app->bind(CreateEmployeeAction::class, function ($app) {
            return new CreateEmployeeAction($app->make(EmployeeCodeGeneratorContract::class));
        });

        $this->app->bind(DeleteEmployeeDocumentAction::class, function ($app) {
            return new DeleteEmployeeDocumentAction($app->make(EmployeeDocumentService::class));
        });
    }

    private function registerPolicies(): void
    {
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(EmployeeDocument::class, EmployeeDocumentPolicy::class);
    }

    private function registerRoutes(): void
    {
        if (config('karyawan.routes.web.enabled', false)) {
            $type = config('karyawan.routes.web.type', 'inertia');
            $routeFile = $type === 'blade' ? 'web-blade.php' : 'web-inertia.php';
            $this->loadRoutesFrom(__DIR__.'/../../routes/'.$routeFile);
        }

        if (config('karyawan.routes.api.enabled', false)) {
            $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        }
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                KaryawanInstallCommand::class,
            ]);
        }
    }

    private function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Config
        $this->publishes([
            __DIR__.'/../../config/karyawan.php' => config_path('karyawan.php'),
        ], 'karyawan-config');

        // Migrations
        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'karyawan-migrations');

        // Factories
        $this->publishes([
            __DIR__.'/../../database/factories' => database_path('factories'),
        ], 'karyawan-factories');

        // API Controllers
        $this->publishes([
            __DIR__.'/../Http/Controllers/Api' => app_path('Http/Controllers/Karyawan/Api'),
        ], 'karyawan-controllers-api');

        // Web Inertia Controllers
        $this->publishes([
            __DIR__.'/../Http/Controllers/Web/Inertia' => app_path('Http/Controllers/Karyawan/Web/Inertia'),
        ], 'karyawan-controllers-web-inertia');

        // Web Blade Controllers
        $this->publishes([
            __DIR__.'/../Http/Controllers/Web/Blade' => app_path('Http/Controllers/Karyawan/Web/Blade'),
        ], 'karyawan-controllers-web-blade');
    }
}
