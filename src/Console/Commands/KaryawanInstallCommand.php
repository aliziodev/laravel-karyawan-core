<?php

namespace Aliziodev\LaravelKaryawanCore\Console\Commands;

use Illuminate\Console\Command;

class KaryawanInstallCommand extends Command
{
    protected $signature = 'karyawan:install';

    protected $description = 'Install package laravel-karyawan-core: publish config, migrations, dan (opsional) controllers.';

    public function handle(): int
    {
        $this->components->info('Instalasi laravel-karyawan-core dimulai.');

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
            }
        }

        // --- 5. Enable routes ---
        if ($this->components->confirm('Aktifkan routes API bawaan package?', false)) {
            $this->updateEnv('KARYAWAN_ROUTES_API_ENABLED', 'true');
            $this->components->info('Routes API diaktifkan via KARYAWAN_ROUTES_API_ENABLED=true');
        }

        if ($this->components->confirm('Aktifkan routes Web bawaan package?', false)) {
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
}
