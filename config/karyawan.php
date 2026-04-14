<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Employee Code Generator
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk generate employee_code secara otomatis.
    | prefix: awalan kode (default: 'EMP')
    | pad_length: panjang angka setelah prefix (default: 5 → EMP00001)
    | auto_generate: aktifkan auto-generate saat create employee
    |
    */
    'employee_code' => [
        'prefix' => env('KARYAWAN_CODE_PREFIX', 'EMP'),
        'pad_length' => (int) env('KARYAWAN_CODE_PAD_LENGTH', 5),
        'auto_generate' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix opsional untuk semua tabel package ini. Kosongkan jika tidak
    | diperlukan. Contoh: 'hr_' → tabel menjadi hr_employees, hr_companies, dst.
    |
    */
    'table_prefix' => env('KARYAWAN_TABLE_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | Nama tabel yang digunakan package ini. Sesuaikan jika ada konflik
    | dengan tabel existing di aplikasi host.
    |
    */
    'table_names' => [
        'employees' => 'employees',
        'companies' => 'companies',
        'branches' => 'branches',
        'departments' => 'departments',
        'positions' => 'positions',
        'employee_documents' => 'employee_documents',
        'employee_emergency_contacts' => 'employee_emergency_contacts',
        'employee_histories' => 'employee_histories',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Model User yang digunakan aplikasi host untuk autentikasi.
    | Package ini tidak mengelola User, hanya menyimpan relasi nullable.
    |
    */
    'user_model' => env('KARYAWAN_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Aktifkan route bawaan package. Secara default dimatikan agar tidak
    | bentrok dengan routing aplikasi host. Jika diaktifkan, route akan
    | tersedia dengan prefix yang dikonfigurasi.
    |
    */
    'routes' => [
        'web' => [
            'enabled' => env('KARYAWAN_ROUTES_WEB_ENABLED', false),
            'type' => env('KARYAWAN_ROUTES_WEB_TYPE', 'inertia'),
            'prefix' => env('KARYAWAN_ROUTES_WEB_PREFIX', 'karyawan'),
            'middleware' => ['web', 'auth'],
        ],
        'api' => [
            'enabled' => env('KARYAWAN_ROUTES_API_ENABLED', false),
            'prefix' => env('KARYAWAN_ROUTES_API_PREFIX', 'api/karyawan'),
            'middleware' => ['api', 'auth:sanctum'],
        ],
    ],

];
