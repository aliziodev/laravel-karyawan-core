<?php

namespace Aliziodev\LaravelKaryawanCore\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class KaryawanInstallCommand extends Command
{
    protected $signature = 'karyawan:install';

    protected $description = 'Install package laravel-karyawan-core: publish config, migrations, dan (opsional) controllers.';

    public function handle(): int
    {
        $this->components->info('Instalasi laravel-karyawan-core dimulai.');

        $publishedApiControllers = false;
        $publishedWebControllers = false;
        $selectedWebType = null;
        $publishedApiRoutes = false;
        $publishedWebRoutes = false;

        // --- 1. Publish config ---
        $this->components->task('Publish config', function () {
            $this->callSilent('vendor:publish', [
                '--tag' => 'karyawan-config',
                '--force' => true,
            ]);
        });

        // --- 2. Custom employee code prefix ---
        $currentPrefix = config('karyawan.employee_code.prefix', 'EMP');
        $prefix = $this->components->ask(
            "Prefix kode karyawan? (saat ini: <comment>{$currentPrefix}</comment>)",
            $currentPrefix
        );

        $currentPad = config('karyawan.employee_code.pad_length', 5);
        $padLength = (int) $this->components->ask(
            "Panjang angka setelah prefix? (saat ini: <comment>{$currentPad}</comment>)",
            (string) $currentPad
        );

        $this->updateEnv('KARYAWAN_CODE_PREFIX', strtoupper(trim($prefix)));
        $this->updateEnv('KARYAWAN_CODE_PAD_LENGTH', (string) $padLength);

        $this->components->info('Kode karyawan akan menggunakan format: <comment>'.strtoupper(trim($prefix)).str_pad('1', $padLength, '0', STR_PAD_LEFT).'</comment>');

        // --- 3. Publish migrations ---
        $this->components->task('Publish migrations', function () {
            $this->callSilent('vendor:publish', [
                '--tag' => 'karyawan-migrations',
                '--force' => true,
            ]);
        });

        // --- 3b. Table prefix ---
        if ($this->components->confirm('Tambah prefix untuk semua tabel package ini?', false)) {
            $tablePrefix = $this->components->ask('Masukkan prefix tabel (contoh: hr_)', '');
            $tablePrefix = trim($tablePrefix);

            if ($tablePrefix !== '') {
                $this->updateEnv('KARYAWAN_TABLE_PREFIX', $tablePrefix);
                $this->components->info("Prefix tabel diset ke: <comment>{$tablePrefix}</comment>");
            }
        }

        // --- 4. Publish controllers (opsional) ---
        if ($this->components->confirm('Publish controllers bawaan package?', false)) {
            $type = $this->components->choice(
                'Publish controller tipe apa?',
                ['api', 'web', 'both'],
                'both'
            );

            if (in_array($type, ['api', 'both'])) {
                $this->components->task('Publish API controllers', function () {
                    $this->callSilent('vendor:publish', [
                        '--tag' => 'karyawan-controllers-api',
                        '--force' => true,
                    ]);
                });

                $publishedApiControllers = true;
            }

            if (in_array($type, ['web', 'both'])) {
                $webType = $this->components->choice(
                    'Web controller menggunakan template apa?',
                    ['inertia', 'blade'],
                    'inertia'
                );

                $tag = $webType === 'blade' ? 'karyawan-controllers-web-blade' : 'karyawan-controllers-web-inertia';
                $this->components->task("Publish Web ({$webType}) controllers", function () use ($tag) {
                    $this->callSilent('vendor:publish', [
                        '--tag' => $tag,
                        '--force' => true,
                    ]);
                });

                $this->updateEnv('KARYAWAN_ROUTES_WEB_TYPE', $webType);
                $this->components->info("Web controller tipe <comment>{$webType}</comment> dipilih.");

                $publishedWebControllers = true;
                $selectedWebType = $webType;
            }

            $this->components->task('Sinkronisasi namespace controller publish', function () {
                $this->normalizePublishedControllerNamespaces();
            });

            if ($publishedApiControllers) {
                $this->components->task('Publish route API host (routes/karyawan/api.php)', function () {
                    $this->callSilent('vendor:publish', [
                        '--tag' => 'karyawan-routes-api',
                        '--force' => true,
                    ]);
                });

                $this->normalizePublishedRouteFile(base_path('routes/karyawan/api.php'));
                $this->ensureRouteInclude(
                    base_path('routes/api.php'),
                    "if (file_exists(base_path('routes/karyawan/api.php'))) {\n    require base_path('routes/karyawan/api.php');\n}"
                );

                $publishedApiRoutes = true;
            }

            if ($publishedWebControllers && in_array($selectedWebType, ['inertia', 'blade'], true)) {
                $webTag = $selectedWebType === 'blade' ? 'karyawan-routes-web-blade' : 'karyawan-routes-web-inertia';
                $webRouteFile = $selectedWebType === 'blade' ? 'web-blade.php' : 'web-inertia.php';

                $this->components->task("Publish route Web host (routes/karyawan/{$webRouteFile})", function () use ($webTag) {
                    $this->callSilent('vendor:publish', [
                        '--tag' => $webTag,
                        '--force' => true,
                    ]);
                });

                $this->normalizePublishedRouteFile(base_path('routes/karyawan/'.$webRouteFile));
                $this->ensureRouteInclude(
                    base_path('routes/web.php'),
                    "if (file_exists(base_path('routes/karyawan/{$webRouteFile}'))) {\n    require base_path('routes/karyawan/{$webRouteFile}');\n}"
                );

                $publishedWebRoutes = true;
            }
        }

        // --- 5. Enable routes ---
        if ($publishedApiRoutes) {
            $this->updateEnv('KARYAWAN_ROUTES_API_ENABLED', 'false');
            $this->components->info('Route API host sudah dipublish. Package route API otomatis dinonaktifkan agar tidak duplikasi.');
        } elseif ($this->components->confirm('Aktifkan routes API bawaan package?', false)) {
            $this->updateEnv('KARYAWAN_ROUTES_API_ENABLED', 'true');
            $this->components->info('Routes API diaktifkan via KARYAWAN_ROUTES_API_ENABLED=true');
        }

        if ($publishedWebRoutes) {
            $this->updateEnv('KARYAWAN_ROUTES_WEB_ENABLED', 'false');
            $this->components->info('Route Web host sudah dipublish. Package route Web otomatis dinonaktifkan agar tidak duplikasi.');
        } elseif ($this->components->confirm('Aktifkan routes Web bawaan package?', false)) {
            $this->updateEnv('KARYAWAN_ROUTES_WEB_ENABLED', 'true');
            $this->components->info('Routes Web diaktifkan via KARYAWAN_ROUTES_WEB_ENABLED=true');
        }

        // --- 6. Run migrations ---
        if ($this->components->confirm('Jalankan migrasi sekarang?', true)) {
            $this->components->task('Running migrations', function () {
                $this->call('migrate');
            });
        }

        $this->components->info('Instalasi selesai. Selamat menggunakan laravel-karyawan-core!');

        return self::SUCCESS;
    }

    private function updateEnv(string $key, string $value): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            return;
        }

        $content = file_get_contents($envPath);

        if (str_contains($content, $key.'=')) {
            $content = preg_replace('/^'.$key.'=.*/m', $key.'='.$value, $content);
        } else {
            $content .= PHP_EOL.$key.'='.$value;
        }

        file_put_contents($envPath, $content);
    }

    private function normalizePublishedControllerNamespaces(): void
    {
        $root = app_path('Http/Controllers/Karyawan');

        if (! is_dir($root)) {
            return;
        }

        foreach ($this->phpFilesIn($root) as $file) {
            $content = file_get_contents($file);

            if ($content === false) {
                continue;
            }

            $updated = preg_replace_callback(
                '/^namespace\\s+Aliziodev\\\\LaravelKaryawanCore\\\\Http\\\\Controllers\\\\(.+);/m',
                static fn (array $matches): string => 'namespace App\\Http\\Controllers\\Karyawan\\'.$matches[1].';',
                $content
            );

            if ($updated === null) {
                continue;
            }

            $updated = str_replace(
                'use Aliziodev\\LaravelKaryawanCore\\Http\\Controllers\\',
                'use App\\Http\\Controllers\\Karyawan\\',
                $updated
            );

            if ($updated !== $content) {
                file_put_contents($file, $updated);
            }
        }
    }

    private function normalizePublishedRouteFile(string $routeFile): void
    {
        if (! is_file($routeFile)) {
            return;
        }

        $content = file_get_contents($routeFile);

        if ($content === false) {
            return;
        }

        $updated = str_replace(
            'use Aliziodev\\LaravelKaryawanCore\\Http\\Controllers\\',
            'use App\\Http\\Controllers\\Karyawan\\',
            $content
        );

        if ($updated !== $content) {
            file_put_contents($routeFile, $updated);
        }
    }

    private function ensureRouteInclude(string $targetRouteFile, string $includeBlock): void
    {
        $directory = dirname($targetRouteFile);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (! file_exists($targetRouteFile)) {
            file_put_contents($targetRouteFile, "<?php\n\n");
        }

        $content = file_get_contents($targetRouteFile);

        if ($content === false || str_contains($content, $includeBlock)) {
            return;
        }

        $content = rtrim($content)."\n\n".$includeBlock."\n";
        file_put_contents($targetRouteFile, $content);
    }

    /**
     * @return array<int, string>
     */
    private function phpFilesIn(string $root): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

        foreach ($iterator as $item) {
            if (! $item->isFile()) {
                continue;
            }

            if ($item->getExtension() !== 'php') {
                continue;
            }

            $files[] = $item->getPathname();
        }

        return $files;
    }
}
