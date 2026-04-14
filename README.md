# laravel-karyawan-core

**Fondasi data karyawan yang dapat digunakan ulang untuk aplikasi bisnis Laravel Indonesia.**

Package Laravel siap produksi yang menyediakan fondasi lengkap manajemen data karyawan — mulai dari struktur organisasi, profil karyawan, manajemen dokumen, kontak darurat, riwayat perubahan, tautan akun pengguna, hingga REST API penuh — semuanya dapat dikonfigurasi dan dipublikasikan ke aplikasi Anda.

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/laravel-%5E11.0%7C%5E12.0-red)](https://laravel.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## Daftar Isi

- [Fitur](#fitur)
- [Persyaratan](#persyaratan)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
  - [Variabel Environment](#variabel-environment)
  - [File Konfigurasi](#file-konfigurasi)
- [Database](#database)
  - [Migrasi](#migrasi)
  - [Prefix Tabel](#prefix-tabel)
  - [Kustomisasi Nama Tabel](#kustomisasi-nama-tabel)
- [Penggunaan](#penggunaan)
  - [Perintah Instalasi Interaktif](#perintah-instalasi-interaktif)
  - [Model](#model)
  - [Actions](#actions)
  - [Services](#services)
  - [Events](#events)
  - [Data Transfer Objects (DTO)](#data-transfer-objects-dto)
- [REST API](#rest-api)
  - [Mengaktifkan Route API](#mengaktifkan-route-api)
  - [Daftar Endpoint API](#daftar-endpoint-api)
  - [Format Respons API](#format-respons-api)
- [Antarmuka Web](#antarmuka-web)
  - [Inertia.js](#inertiajs)
  - [Blade](#blade)
  - [Nama Route Web](#nama-route-web)
- [Publish Controllers](#publish-controllers)
- [Generator Kode Karyawan](#generator-kode-karyawan)
- [Otorisasi (Policies)](#otorisasi-policies)
- [Referensi Enum](#referensi-enum)
- [Penggunaan Lanjutan](#penggunaan-lanjutan)
  - [Mendengarkan Events](#mendengarkan-events)
  - [Mengganti Generator Kode Karyawan](#mengganti-generator-kode-karyawan)
  - [Mengganti Policies](#mengganti-policies)
  - [Memperluas Model](#memperluas-model)
  - [Menggunakan Model User yang Berbeda](#menggunakan-model-user-yang-berbeda)
  - [Menangani Exception](#menangani-exception)
- [Pengujian](#pengujian)
- [Lisensi](#lisensi)

---

## Fitur

- **Struktur Organisasi** — Manajemen Perusahaan, Cabang, Departemen, dan Jabatan lengkap dengan CRUD
- **Manajemen Karyawan** — Profil karyawan lengkap meliputi data pribadi, data ketenagakerjaan, dan identitas
- **Auto-Generate Kode Karyawan** — Prefix dan panjang angka dapat dikonfigurasi, aman dari race condition
- **Pelacakan Status** — Aktif, Tidak Aktif, Mengundurkan Diri, PHK, Pensiun, Cuti Panjang beserta riwayatnya
- **Manajemen Dokumen** — Upload file beserta metadata (tipe, nomor dokumen, masa berlaku), verifikasi checksum
- **Kontak Darurat** — Beberapa kontak per karyawan dengan penanda kontak utama
- **Log Riwayat** — Jejak audit otomatis untuk setiap perubahan status dan tautan akun
- **Tautan Akun Pengguna** — Hubungkan/putuskan model User mana pun ke karyawan, aman dari concurrency
- **REST API** — JSON API lengkap dengan dukungan Laravel Sanctum, respons terpaginasi, dan JSON Resources
- **Controller Web** — Tersedia dalam versi **Inertia.js** maupun **Blade**
- **Events** — Sistem event lengkap untuk setiap siklus hidup karyawan
- **Policies** — Otorisasi berbasis Policy yang dapat diperluas untuk semua model
- **Perintah Instalasi Artisan** — Wizard interaktif `php artisan karyawan:install`

---

## Persyaratan

| Dependensi | Versi |
|---|---|
| PHP | ^8.2 \| ^8.3 \| ^8.4 |
| Laravel | ^11.0 \| ^12.0 |
| illuminate/support | ^11.0 \| ^12.0 |

> **Opsional:** `inertiajs/inertia-laravel` hanya diperlukan apabila Anda menggunakan controller web versi Inertia.

---

## Instalasi

Pasang package melalui Composer:

```bash
composer require aliziodev/laravel-karyawan-core
```

Package ini menggunakan auto-discovery Laravel. Service provider terdaftar secara otomatis.

---

## Konfigurasi

### Perintah Instalasi Interaktif

Cara tercepat untuk menyiapkan package adalah melalui wizard instalasi interaktif:

```bash
php artisan karyawan:install
```

Wizard akan memandu Anda melalui langkah-langkah berikut:

1. **Publish config** — menyalin `config/karyawan.php` ke aplikasi Anda
2. **Prefix kode karyawan** — misalnya `EMP` → menghasilkan `EMP00001`, `EMP00002`, …
3. **Panjang digit kode** — jumlah angka setelah prefix (default: `5`)
4. **Publish migrasi** — menyalin semua file migrasi ke `database/migrations`
5. **Prefix tabel** — prefix opsional untuk semua tabel (contoh: `hr_` → `hr_employees`)
6. **Publish controllers** — pilih API, Web Inertia, Web Blade, atau semua
7. **Mengaktifkan route** — toggle route API dan/atau Web bawaan
8. **Menjalankan migrasi** — opsional menjalankan `php artisan migrate` langsung

### Variabel Environment

Tambahkan variabel berikut ke file `.env` Anda sesuai kebutuhan:

```dotenv
# Generate kode karyawan
KARYAWAN_CODE_PREFIX=EMP
KARYAWAN_CODE_PAD_LENGTH=5

# Prefix tabel (opsional, dibiarkan kosong jika tidak diperlukan)
KARYAWAN_TABLE_PREFIX=

# Model User (default: App\Models\User)
KARYAWAN_USER_MODEL=App\Models\User

# Route API
KARYAWAN_ROUTES_API_ENABLED=false
KARYAWAN_ROUTES_API_PREFIX=api/karyawan

# Route Web
KARYAWAN_ROUTES_WEB_ENABLED=false
KARYAWAN_ROUTES_WEB_TYPE=inertia
KARYAWAN_ROUTES_WEB_PREFIX=karyawan
```

### File Konfigurasi

Publish file konfigurasi secara manual jika diperlukan:

```bash
php artisan vendor:publish --tag=karyawan-config
```

Referensi konfigurasi lengkap (`config/karyawan.php`):

```php
return [

    'employee_code' => [
        'prefix'        => env('KARYAWAN_CODE_PREFIX', 'EMP'),
        'pad_length'    => (int) env('KARYAWAN_CODE_PAD_LENGTH', 5),
        'auto_generate' => true,
    ],

    'table_prefix' => env('KARYAWAN_TABLE_PREFIX', ''),

    'table_names' => [
        'employees'                   => 'employees',
        'companies'                   => 'companies',
        'branches'                    => 'branches',
        'departments'                 => 'departments',
        'positions'                   => 'positions',
        'employee_documents'          => 'employee_documents',
        'employee_emergency_contacts' => 'employee_emergency_contacts',
        'employee_histories'          => 'employee_histories',
    ],

    'user_model' => env('KARYAWAN_USER_MODEL', 'App\\Models\\User'),

    'routes' => [
        'web' => [
            'enabled'    => env('KARYAWAN_ROUTES_WEB_ENABLED', false),
            'type'       => env('KARYAWAN_ROUTES_WEB_TYPE', 'inertia'), // 'inertia' | 'blade'
            'prefix'     => env('KARYAWAN_ROUTES_WEB_PREFIX', 'karyawan'),
            'middleware' => ['web', 'auth'],
        ],
        'api' => [
            'enabled'    => env('KARYAWAN_ROUTES_API_ENABLED', false),
            'prefix'     => env('KARYAWAN_ROUTES_API_PREFIX', 'api/karyawan'),
            'middleware' => ['api', 'auth:sanctum'],
        ],
    ],

];
```

---

## Database

### Migrasi

Publish dan jalankan migrasi:

```bash
php artisan vendor:publish --tag=karyawan-migrations
php artisan migrate
```

Package ini membuat **8 tabel**:

| Tabel | Keterangan |
|---|---|
| `companies` | Data master perusahaan |
| `branches` | Kantor cabang per perusahaan |
| `departments` | Departemen (dapat di-scope per perusahaan) |
| `positions` | Jabatan (dapat di-scope per perusahaan) |
| `employees` | Profil karyawan lengkap dengan soft delete |
| `employee_documents` | File dokumen beserta metadata |
| `employee_emergency_contacts` | Kontak darurat karyawan |
| `employee_histories` | Jejak audit untuk setiap perubahan signifikan |

### Prefix Tabel

Jika aplikasi Anda sudah memiliki nama tabel yang bentrok, atur prefix terlebih dahulu:

```dotenv
KARYAWAN_TABLE_PREFIX=hr_
```

Semua 8 tabel akan menggunakan prefix tersebut: `hr_employees`, `hr_companies`, dan seterusnya.

> Prefix diterapkan secara otomatis melalui method `getTable()` pada setiap model. Tidak ada perubahan pada file migrasi — cukup set prefix sebelum menjalankan migrasi.

### Kustomisasi Nama Tabel

Untuk kendali penuh atas nama tabel individual, publish config dan perbarui bagian `table_names`:

```php
'table_names' => [
    'employees' => 'staff',
    'companies' => 'organizations',
    // ...
],
```

---

## Penggunaan

### Model

Semua model dapat diakses langsung dari namespace package:

```php
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Aliziodev\LaravelKaryawanCore\Models\Branch;
use Aliziodev\LaravelKaryawanCore\Models\Department;
use Aliziodev\LaravelKaryawanCore\Models\Position;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeDocument;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeEmergencyContact;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeHistory;
```

#### Company

```php
// Membuat perusahaan baru
$company = Company::create([
    'code'      => 'PT001',
    'name'      => 'PT Maju Bersama',
    'email'     => 'info@majubersama.co.id',
    'phone'     => '021-5551234',
    'is_active' => true,
]);

// Mengambil hanya yang aktif
$aktif = Company::active()->get();

// Relasi
$company->branches;     // Koleksi Branch
$company->departments;  // Koleksi Department
$company->positions;    // Koleksi Position
$company->employees;    // Koleksi Employee
```

#### Branch, Department, Position

```php
// Filter berdasarkan perusahaan
Branch::byCompany($companyId)->active()->get();
Department::byCompany($companyId)->get();
Position::active()->get();
```

#### Employee — Scope

```php
// Karyawan aktif saja
Employee::active()->get();

// Filter berdasarkan organisasi
Employee::byCompany($companyId)->get();
Employee::byDepartment($departmentId)->get();
Employee::byBranch($branchId)->get();
Employee::byPosition($positionId)->get();

// Pencarian berdasarkan nama, kode, atau email
Employee::search('Budi')->get();

// Karyawan yang sudah/belum memiliki akun login
Employee::withLogin()->get();
Employee::withoutLogin()->get();

// Eager load relasi
Employee::with(['company', 'branch', 'department', 'position', 'manager'])->get();

// Termasuk yang sudah dihapus (soft delete)
Employee::withTrashed()->find($id);
```

#### Employee — Relasi

```php
$employee->company;           // Company
$employee->branch;            // Branch
$employee->department;        // Department
$employee->position;          // Position
$employee->manager;           // Employee (self-referencing — atasan langsung)
$employee->subordinates;      // Koleksi Employee (bawahan)
$employee->documents;         // Koleksi EmployeeDocument
$employee->emergencyContacts; // Koleksi EmployeeEmergencyContact
$employee->histories;         // Koleksi EmployeeHistory (terbaru lebih dahulu)
```

#### Employee — Method Pembantu

```php
$employee->hasLogin();   // bool — apakah sudah terhubung ke akun user
$employee->isActive();   // bool — active_status === 'active'
$employee->isWorking();  // bool — aktif bekerja atau sedang cuti panjang
```

---

### Actions

Action mengenkapsulasi satu operasi bisnis. Setiap action terdaftar di container IoC Laravel dan digunakan melalui **constructor injection** di controller atau service.

#### Membuat Karyawan

```php
use Aliziodev\LaravelKaryawanCore\Actions\CreateEmployeeAction;
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData;
use Aliziodev\LaravelKaryawanCore\Enums\EmploymentType;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly CreateEmployeeAction $createAction,
    ) {}

    public function store(CreateEmployeeRequest $request): RedirectResponse
    {
        $employee = $this->createAction->execute(
            EmployeeData::fromRequest($request)
        );

        // Kode karyawan di-generate otomatis: EMP00001
        return redirect()->route('employees.show', $employee);
    }
}
```

#### Memperbarui Karyawan

```php
use Aliziodev\LaravelKaryawanCore\Actions\UpdateEmployeeAction;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly UpdateEmployeeAction $updateAction,
    ) {}

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        // employee_code tidak dapat diubah melalui action ini
        $this->updateAction->execute($employee, EmployeeData::fromRequest($request));

        return redirect()->route('employees.show', $employee);
    }
}
```

> `UpdateEmployeeAction` hanya mendispatch event `EmployeeUpdated` apabila ada atribut yang benar-benar berubah.

#### Mengubah Status Karyawan

```php
use Aliziodev\LaravelKaryawanCore\Actions\ChangeEmployeeStatusAction;
use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;

class EmployeeStatusController extends Controller
{
    public function __construct(
        private readonly ChangeEmployeeStatusAction $changeStatusAction,
    ) {}

    public function __invoke(ChangeEmployeeStatusRequest $request, Employee $employee): RedirectResponse
    {
        $this->changeStatusAction->execute(
            employee:      $employee,
            newStatus:     ActiveStatus::from($request->active_status),
            effectiveDate: $request->effective_date,
            notes:         $request->notes,
            createdBy:     $request->user()?->id,
        );

        // Secara otomatis:
        // - Mengisi exit_date untuk status Resigned / Terminated / Retired
        // - Membuat catatan EmployeeHistory
        // - Mendispatch event EmployeeStatusChanged

        return back()->with('success', 'Status karyawan berhasil diubah.');
    }
}
```

#### Menghubungkan / Memutuskan Akun User

```php
use Aliziodev\LaravelKaryawanCore\Actions\LinkEmployeeUserAction;
use Aliziodev\LaravelKaryawanCore\Actions\UnlinkEmployeeUserAction;

class EmployeeUserController extends Controller
{
    public function __construct(
        private readonly LinkEmployeeUserAction   $linkUserAction,
        private readonly UnlinkEmployeeUserAction $unlinkUserAction,
    ) {}

    public function store(LinkEmployeeUserRequest $request, Employee $employee): RedirectResponse
    {
        $this->linkUserAction->execute(
            employee:  $employee,
            userId:    (int) $request->user_id,
            createdBy: $request->user()?->id,
        );

        return back()->with('success', 'Akun login berhasil dikaitkan.');
    }

    public function destroy(Request $request, Employee $employee): RedirectResponse
    {
        $this->unlinkUserAction->execute(
            employee:  $employee,
            createdBy: $request->user()?->id,
        );

        return back()->with('success', 'Akun login berhasil dilepas.');
    }
}
```

Kedua action menggunakan **pessimistic locking** untuk mencegah race condition, dan melempar `EmployeeUserLinkException` apabila operasi tidak valid.

#### Menyimpan / Menghapus Dokumen

```php
use Aliziodev\LaravelKaryawanCore\Actions\StoreEmployeeDocumentAction;
use Aliziodev\LaravelKaryawanCore\Actions\DeleteEmployeeDocumentAction;

class EmployeeDocumentController extends Controller
{
    public function __construct(
        private readonly StoreEmployeeDocumentAction  $storeDocumentAction,
        private readonly DeleteEmployeeDocumentAction $deleteDocumentAction,
    ) {}

    public function store(StoreDocumentRequest $request, Employee $employee): RedirectResponse
    {
        $this->storeDocumentAction->execute(
            $employee,
            $request->toData($employee) // mengonversi request ke EmployeeDocumentData
        );

        return back()->with('success', 'Dokumen berhasil disimpan.');
    }

    public function destroy(Employee $employee, EmployeeDocument $document): RedirectResponse
    {
        abort_unless($document->employee_id === $employee->id, 404);

        $this->deleteDocumentAction->execute($document);
        // Menghapus record database dan file dari storage secara bersamaan

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
```

---

### Services

`EmployeeService` adalah singleton yang membungkus semua action dalam satu class yang mudah digunakan. Cocok untuk digunakan di luar controller, misalnya di job, command, atau service lain.

```php
use Aliziodev\LaravelKaryawanCore\Services\EmployeeService;
use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;

class SomeJob implements ShouldQueue
{
    public function __construct(
        private readonly EmployeeService $employeeService,
    ) {}

    public function handle(): void
    {
        $employee = $this->employeeService->create($employeeData);
        $employee = $this->employeeService->update($employee, $employeeData);
        $employee = $this->employeeService->changeStatus($employee, ActiveStatus::Inactive);
        $employee = $this->employeeService->linkUser($employee, $userId);
        $employee = $this->employeeService->unlinkUser($employee);
        $document = $this->employeeService->storeDocument($employee, $documentData);
        $this->employeeService->deleteDocument($document);
    }
}
```

#### EmployeeDocumentService

Digunakan untuk menangani operasi file pada storage:

```php
use Aliziodev\LaravelKaryawanCore\Services\EmployeeDocumentService;

class EmployeeDocumentController extends Controller
{
    public function __construct(
        private readonly EmployeeDocumentService $documentService,
    ) {}

    public function getUrl(Employee $employee, EmployeeDocument $document): JsonResponse
    {
        // URL sementara (untuk S3/cloud) atau URL permanen (untuk disk lokal)
        $url = $this->documentService->getTemporaryUrl($document, minutes: 60);

        return response()->json(['url' => $url]);
    }
}
```

Method yang tersedia:

| Method | Keterangan |
|---|---|
| `storeFile($file, $employee, $disk, $folder)` | Menyimpan file dan mengembalikan metadata (path, nama, ukuran, checksum) |
| `deleteFile($document)` | Menghapus file dari storage |
| `getTemporaryUrl($document, $minutes)` | Menghasilkan URL akses file |

---

### Events

Semua event didispatch **di luar** transaksi database sehingga listener selalu menerima data yang sudah tersimpan.

| Event | Didispatch Oleh | Properti |
|---|---|---|
| `EmployeeCreated` | `CreateEmployeeAction` | `$employee` |
| `EmployeeUpdated` | `UpdateEmployeeAction` | `$employee`, `$changedAttributes` |
| `EmployeeStatusChanged` | `ChangeEmployeeStatusAction` | `$employee`, `$previousStatus`, `$newStatus` |
| `EmployeeLinkedToUser` | `LinkEmployeeUserAction` | `$employee`, `$userId` |
| `EmployeeUnlinkedFromUser` | `UnlinkEmployeeUserAction` | `$employee`, `$previousUserId` |

---

### Data Transfer Objects (DTO)

DTO adalah class `readonly` PHP 8.2 yang digunakan untuk meneruskan data ke action.

#### EmployeeData

```php
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeData;

// Dari array
$data = EmployeeData::fromArray([
    'full_name'       => 'Budi Santoso',
    'company_id'      => 1,
    'employment_type' => 'permanent',
    'join_date'       => '2025-01-01',
]);

// Dari FormRequest
$data = EmployeeData::fromRequest($request);

// Konversi ke array (nilai null tidak disertakan)
$array = $data->toArray();
```

#### EmployeeDocumentData

```php
use Aliziodev\LaravelKaryawanCore\DataTransferObjects\EmployeeDocumentData;

$data = EmployeeDocumentData::fromArray([
    'type'      => 'ktp',
    'name'      => 'KTP Budi Santoso',
    'file_disk' => 'local',
    'file_path' => 'employee-documents/EMP00001/ktp.jpg',
    'file_name' => 'ktp.jpg',
]);
```

---

## REST API

### Mengaktifkan Route API

```dotenv
KARYAWAN_ROUTES_API_ENABLED=true
KARYAWAN_ROUTES_API_PREFIX=api/karyawan
```

Middleware default adalah `['api', 'auth:sanctum']`. Untuk menggantinya:

```php
// config/karyawan.php
'api' => [
    'enabled'    => true,
    'prefix'     => 'api/hr',
    'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
],
```

### Daftar Endpoint API

Semua endpoint menggunakan prefix yang dikonfigurasi (default: `api/karyawan`).

#### Perusahaan

| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/companies` | Daftar terpaginasi. Mendukung `?search=`, `?active_only=1` |
| `POST` | `/companies` | Buat perusahaan baru |
| `GET` | `/companies/{id}` | Detail perusahaan beserta cabang, departemen, jabatan |
| `PUT` | `/companies/{id}` | Perbarui perusahaan |
| `DELETE` | `/companies/{id}` | Hapus perusahaan |

#### Cabang

| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/branches` | Daftar terpaginasi. Mendukung `?company_id=`, `?active_only=1` |
| `POST` | `/branches` | Buat cabang baru |
| `GET` | `/branches/{id}` | Detail cabang |
| `PUT` | `/branches/{id}` | Perbarui cabang |
| `DELETE` | `/branches/{id}` | Hapus cabang |

#### Departemen

| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/departments` | Daftar terpaginasi. Mendukung `?company_id=`, `?active_only=1` |
| `POST` | `/departments` | Buat departemen baru |
| `GET` | `/departments/{id}` | Detail departemen |
| `PUT` | `/departments/{id}` | Perbarui departemen |
| `DELETE` | `/departments/{id}` | Hapus departemen |

#### Jabatan

| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/positions` | Daftar terpaginasi. Mendukung `?company_id=`, `?active_only=1` |
| `POST` | `/positions` | Buat jabatan baru |
| `GET` | `/positions/{id}` | Detail jabatan |
| `PUT` | `/positions/{id}` | Perbarui jabatan |
| `DELETE` | `/positions/{id}` | Hapus jabatan |

#### Karyawan

| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/employees` | Daftar terpaginasi. Mendukung `?search=`, `?company_id=`, `?department_id=`, `?active_only=1` |
| `POST` | `/employees` | Buat karyawan baru |
| `GET` | `/employees/{id}` | Detail karyawan beserta semua relasinya |
| `PUT` | `/employees/{id}` | Perbarui karyawan |
| `DELETE` | `/employees/{id}` | Soft-delete karyawan |

#### Sub-Resource Karyawan

Semua endpoint di bawah ini menggunakan prefix `/employees/{employee}`:

| Method | Endpoint | Keterangan |
|---|---|---|
| `PATCH` | `/employees/{employee}/status` | Ubah status karyawan |
| `POST` | `/employees/{employee}/user` | Kaitkan karyawan ke akun user |
| `DELETE` | `/employees/{employee}/user` | Putuskan kaitan karyawan dari akun user |
| `GET` | `/employees/{employee}/documents` | Daftar dokumen |
| `POST` | `/employees/{employee}/documents` | Unggah dokumen |
| `DELETE` | `/employees/{employee}/documents/{document}` | Hapus dokumen |
| `GET` | `/employees/{employee}/emergency-contacts` | Daftar kontak darurat |
| `POST` | `/employees/{employee}/emergency-contacts` | Tambah kontak darurat |
| `PUT` | `/employees/{employee}/emergency-contacts/{contact}` | Perbarui kontak darurat |
| `DELETE` | `/employees/{employee}/emergency-contacts/{contact}` | Hapus kontak darurat |
| `GET` | `/employees/{employee}/histories` | Riwayat perubahan karyawan |

### Format Respons API

Semua endpoint daftar mengembalikan respons terpaginasi:

```json
{
    "data": [
        {
            "id": 1,
            "employee_code": "EMP00001",
            "full_name": "Budi Santoso",
            "active_status": "active",
            "active_status_label": "Aktif",
            "company_id": 1,
            "has_login": false,
            "is_active": true
        }
    ],
    "meta": {
        "total": 50,
        "per_page": 20,
        "current_page": 1,
        "last_page": 3
    },
    "links": {
        "first": "...",
        "last": "...",
        "prev": null,
        "next": "..."
    }
}
```

Respons resource tunggal:

```json
{
    "data": {
        "id": 1,
        "employee_code": "EMP00001",
        "full_name": "Budi Santoso",
        "work_email": "budi@perusahaan.co.id",
        "gender": "male",
        "gender_label": "Laki-laki",
        "employment_type": "permanent",
        "employment_type_label": "Karyawan Tetap",
        "active_status": "active",
        "active_status_label": "Aktif",
        "company": { "id": 1, "name": "PT Maju Bersama" },
        "department": { "id": 2, "name": "Engineering" },
        "position": { "id": 3, "name": "Senior Developer" }
    }
}
```

#### Body Request: Ubah Status

```json
{
    "active_status": "resigned",
    "effective_date": "2024-12-31",
    "notes": "Karyawan mengundurkan diri atas kemauan sendiri."
}
```

#### Body Request: Kaitkan Akun User

```json
{
    "user_id": 42
}
```

#### Body Request: Buat Karyawan

```json
{
    "full_name": "Rina Firgina",
    "work_email": "siti@perusahaan.co.id",
    "company_id": 1,
    "branch_id": 2,
    "department_id": 3,
    "position_id": 4,
    "employment_type": "permanent",
    "join_date": "2025-01-15",
    "gender": "female",
    "religion": "islam",
    "marital_status": "single"
}
```

---

## Antarmuka Web

### Inertia.js

Atur `KARYAWAN_ROUTES_WEB_TYPE=inertia` dan `KARYAWAN_ROUTES_WEB_ENABLED=true`.

Controller merender komponen Inertia menggunakan path kebab-case:

| Controller | Path Komponen |
|---|---|
| `CompanyController` | `karyawan/company/index`, `karyawan/company/create`, `karyawan/company/show`, `karyawan/company/edit` |
| `BranchController` | `karyawan/branch/index`, `karyawan/branch/create`, `karyawan/branch/show`, `karyawan/branch/edit` |
| `DepartmentController` | `karyawan/department/index`, … |
| `PositionController` | `karyawan/position/index`, … |
| `EmployeeController` | `karyawan/employee/index`, `karyawan/employee/create`, `karyawan/employee/show`, `karyawan/employee/edit` |
| `EmployeeDocumentController` | `karyawan/employee/document/index` |
| `EmployeeEmergencyContactController` | `karyawan/employee/emergency-contact/index`, `karyawan/employee/emergency-contact/create`, `karyawan/employee/emergency-contact/edit` |
| `EmployeeHistoryController` | `karyawan/employee/history/index` |

Buat komponen Vue/React yang sesuai di frontend Anda pada path-path tersebut.

### Blade

Atur `KARYAWAN_ROUTES_WEB_TYPE=blade` dan `KARYAWAN_ROUTES_WEB_ENABLED=true`.

Controller merender Blade view menggunakan notasi titik:

| Controller | Path View |
|---|---|
| `CompanyController` | `karyawan.company.index`, `karyawan.company.create`, `karyawan.company.show`, `karyawan.company.edit` |
| `BranchController` | `karyawan.branch.index`, … |
| `DepartmentController` | `karyawan.department.index`, … |
| `PositionController` | `karyawan.position.index`, … |
| `EmployeeController` | `karyawan.employee.index`, `karyawan.employee.create`, `karyawan.employee.show`, `karyawan.employee.edit` |
| `EmployeeDocumentController` | `karyawan.employee.document.index` |
| `EmployeeEmergencyContactController` | `karyawan.employee.emergency-contact.index`, `karyawan.employee.emergency-contact.create`, `karyawan.employee.emergency-contact.edit` |
| `EmployeeHistoryController` | `karyawan.employee.history.index` |

Buat template Blade yang sesuai di `resources/views/karyawan/`.

### Nama Route Web

Semua route web menggunakan nama yang sama tanpa memandang tipe Inertia atau Blade:

```
karyawan.companies.index / create / store / show / edit / update / destroy
karyawan.branches.index / create / store / show / edit / update / destroy
karyawan.departments.index / create / store / show / edit / update / destroy
karyawan.positions.index / create / store / show / edit / update / destroy
karyawan.employees.index / create / store / show / edit / update / destroy

karyawan.employees.status                    (PATCH)
karyawan.employees.user.store                (POST)
karyawan.employees.user.destroy              (DELETE)
karyawan.employees.documents.index           (GET)
karyawan.employees.documents.store           (POST)
karyawan.employees.documents.destroy         (DELETE)
karyawan.employees.emergency-contacts.index  (GET)
karyawan.employees.emergency-contacts.create (GET)
karyawan.employees.emergency-contacts.store  (POST)
karyawan.employees.emergency-contacts.edit   (GET)
karyawan.employees.emergency-contacts.update (PUT)
karyawan.employees.emergency-contacts.destroy (DELETE)
karyawan.employees.histories.index           (GET)
```

---

## Publish Controllers

Publish controller ke aplikasi Anda untuk kustomisasi penuh:

```bash
# Controller API saja
php artisan vendor:publish --tag=karyawan-controllers-api

# Controller Web versi Inertia.js
php artisan vendor:publish --tag=karyawan-controllers-web-inertia

# Controller Web versi Blade
php artisan vendor:publish --tag=karyawan-controllers-web-blade
```

Controller disalin ke:

| Tag | Tujuan |
|---|---|
| `karyawan-controllers-api` | `app/Http/Controllers/Karyawan/Api/` |
| `karyawan-controllers-web-inertia` | `app/Http/Controllers/Karyawan/Web/Inertia/` |
| `karyawan-controllers-web-blade` | `app/Http/Controllers/Karyawan/Web/Blade/` |

Setelah publish, perbarui namespace di setiap controller dari `Aliziodev\LaravelKaryawanCore\Http\Controllers\` menjadi `App\Http\Controllers\Karyawan\` dan arahkan route Anda ke controller yang baru.

---

## Generator Kode Karyawan

Kode dihasilkan dengan format `{PREFIX}{angka}`, contoh: `EMP00001`.

```dotenv
KARYAWAN_CODE_PREFIX=EMP
KARYAWAN_CODE_PAD_LENGTH=5
```

Dengan konfigurasi di atas, karyawan akan mendapatkan kode: `EMP00001`, `EMP00002`, …, `EMP99999`.

Generator menggunakan **pessimistic locking** (`lockForUpdate`) untuk mencegah kode duplikat saat ada request serentak. Kode karyawan yang sudah dihapus (soft delete) tidak akan pernah digunakan ulang.

---

## Otorisasi (Policies)

Package ini dilengkapi policy default yang terbuka (semua diizinkan). Override di `AuthServiceProvider` Anda untuk membatasi akses:

```php
// app/Policies/EmployeePolicy.php

use Aliziodev\LaravelKaryawanCore\Policies\EmployeePolicy as BasePolicy;
use Aliziodev\LaravelKaryawanCore\Models\Employee;

class EmployeePolicy extends BasePolicy
{
    public function viewSensitive(mixed $user, Employee $employee): bool
    {
        // Field sensitif: NIK, nomor KK, NPWP
        return $user->hasRole('hr-admin') || $user->id === $employee->user_id;
    }

    public function delete(mixed $user, Employee $employee): bool
    {
        return $user->hasRole('hr-admin');
    }
}
```

Daftarkan di `AuthServiceProvider`:

```php
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use App\Policies\EmployeePolicy;

protected $policies = [
    Employee::class => EmployeePolicy::class,
];
```

#### Method Policy yang Tersedia

**EmployeePolicy:** `viewAny`, `view`, `viewSensitive`, `create`, `update`, `delete`, `changeStatus`, `linkUser`, `unlinkUser`

**EmployeeDocumentPolicy:** `viewAny`, `view`, `create`, `delete`

---

## Referensi Enum

Semua enum mengimplementasikan method `label(): string` yang mengembalikan label dalam bahasa Indonesia.

### ActiveStatus

| Value | Label | `isWorking()` |
|---|---|---|
| `active` | Aktif | `true` |
| `inactive` | Tidak Aktif | `false` |
| `resigned` | Mengundurkan Diri | `false` |
| `terminated` | PHK | `false` |
| `retired` | Pensiun | `false` |
| `long_leave` | Cuti Panjang | `true` |

### EmploymentType

| Value | Label |
|---|---|
| `permanent` | Karyawan Tetap |
| `contract` | Karyawan Kontrak |
| `internship` | Magang |
| `daily_worker` | Pekerja Harian Lepas |
| `outsourcing` | Outsourcing |

### DocumentType

| Value | Label |
|---|---|
| `ktp` | KTP |
| `kk` | Kartu Keluarga |
| `npwp` | NPWP |
| `contract` | Kontrak Kerja |
| `certificate` | Sertifikat |
| `diploma` | Ijazah |
| `cv` | CV |
| `other` | Lainnya |

### Gender

| Value | Label |
|---|---|
| `male` | Laki-laki |
| `female` | Perempuan |

### Religion

| Value | Label |
|---|---|
| `islam` | Islam |
| `kristen` | Kristen |
| `katolik` | Katolik |
| `hindu` | Hindu |
| `buddha` | Buddha |
| `konghucu` | Konghucu |
| `other` | Lainnya |

### MaritalStatus

| Value | Label |
|---|---|
| `single` | Belum Menikah |
| `married` | Menikah |
| `divorced` | Cerai Hidup |
| `widowed` | Cerai Mati |

### HistoryType

| Value | Label |
|---|---|
| `status_change` | Perubahan Status |
| `position_change` | Perubahan Jabatan |
| `department_change` | Perubahan Departemen |
| `branch_change` | Perubahan Cabang |
| `company_change` | Perubahan Perusahaan |
| `employment_type_change` | Perubahan Jenis Hubungan Kerja |
| `manager_change` | Perubahan Atasan |
| `user_linked` | Akun Login Dikaitkan |
| `user_unlinked` | Akun Login Dilepas |

---

## Penggunaan Lanjutan

### Mendengarkan Events

Daftarkan listener di `EventServiceProvider`:

```php
// app/Providers/EventServiceProvider.php

protected $listen = [
    \Aliziodev\LaravelKaryawanCore\Events\EmployeeCreated::class        => [
        \App\Listeners\KirimEmailSelamatDatang::class,
    ],
    \Aliziodev\LaravelKaryawanCore\Events\EmployeeStatusChanged::class  => [
        \App\Listeners\CabutAksesKaryawan::class,
    ],
    \Aliziodev\LaravelKaryawanCore\Events\EmployeeLinkedToUser::class   => [
        \App\Listeners\SinkronisasiHakAkses::class,
    ],
];
```

Contoh implementasi listener:

```php
// app/Listeners/KirimEmailSelamatDatang.php

use Aliziodev\LaravelKaryawanCore\Events\EmployeeCreated;

class KirimEmailSelamatDatang
{
    public function handle(EmployeeCreated $event): void
    {
        $employee = $event->employee;

        if ($employee->work_email) {
            Mail::to($employee->work_email)->send(new EmailSelamatDatang($employee));
        }
    }
}
```

```php
// app/Listeners/CabutAksesKaryawan.php

use Aliziodev\LaravelKaryawanCore\Events\EmployeeStatusChanged;
use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;

class CabutAksesKaryawan
{
    public function handle(EmployeeStatusChanged $event): void
    {
        $statusTidakAktif = [
            ActiveStatus::Resigned,
            ActiveStatus::Terminated,
            ActiveStatus::Retired,
        ];

        if (in_array($event->newStatus, $statusTidakAktif)) {
            // Cabut semua token akses
            $event->employee->user?->tokens()->delete();
        }
    }
}
```

```php
// app/Listeners/SinkronisasiHakAkses.php

use Aliziodev\LaravelKaryawanCore\Events\EmployeeLinkedToUser;

class SinkronisasiHakAkses
{
    public function handle(EmployeeLinkedToUser $event): void
    {
        $employee = $event->employee;

        // Berikan role berdasarkan jabatan karyawan
        $employee->user?->assignRole($employee->position->name);
    }
}
```

### Mengganti Generator Kode Karyawan

Buat implementasi kustom yang mengimplementasikan kontrak:

```php
// app/Services/GeneratorKodeKaryawan.php

use Aliziodev\LaravelKaryawanCore\Contracts\EmployeeCodeGeneratorContract;
use Aliziodev\LaravelKaryawanCore\Models\Employee;

class GeneratorKodeKaryawan implements EmployeeCodeGeneratorContract
{
    public function generate(?string $prefix = null): string
    {
        // Contoh: format berbasis tahun → EMP2025-001
        $tahun  = date('Y');
        $jumlah = Employee::whereYear('join_date', $tahun)->count() + 1;

        return sprintf('%s%s-%03d', $prefix ?? 'EMP', $tahun, $jumlah);
    }
}
```

Daftarkan binding di service provider Anda:

```php
// app/Providers/AppServiceProvider.php

use Aliziodev\LaravelKaryawanCore\Contracts\EmployeeCodeGeneratorContract;
use App\Services\GeneratorKodeKaryawan;

public function register(): void
{
    $this->app->bind(EmployeeCodeGeneratorContract::class, GeneratorKodeKaryawan::class);
}
```

### Mengganti Policies

Override method spesifik dengan memperluas class policy bawaan:

```php
// app/Policies/EmployeePolicy.php

use Aliziodev\LaravelKaryawanCore\Policies\EmployeePolicy as BasePolicy;

class EmployeePolicy extends BasePolicy
{
    public function viewSensitive(mixed $user, Employee $employee): bool
    {
        return $user->hasPermissionTo('lihat-data-sensitif-karyawan');
    }

    public function delete(mixed $user, Employee $employee): bool
    {
        return $user->hasRole('hr-admin');
    }

    public function changeStatus(mixed $user, Employee $employee): bool
    {
        return $user->hasRole(['hr-admin', 'hr-manager']);
    }
}
```

### Memperluas Model

Model package menggunakan `config('karyawan.table_names.*')` untuk resolusi tabel, sehingga dapat diperluas dengan bebas:

```php
// app/Models/Employee.php

use Aliziodev\LaravelKaryawanCore\Models\Employee as BaseEmployee;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends BaseEmployee
{
    public function gajiPokok(): HasMany
    {
        return $this->hasMany(GajiPokok::class);
    }

    public function scopeByGolongan($query, string $golongan)
    {
        return $query->where('golongan', $golongan);
    }
}
```

Daftarkan binding agar action bawaan package menggunakan model yang diperluas:

```php
// app/Providers/AppServiceProvider.php

public function register(): void
{
    $this->app->bind(
        \Aliziodev\LaravelKaryawanCore\Models\Employee::class,
        \App\Models\Employee::class
    );
}
```

### Menggunakan Model User yang Berbeda

Secara default package merujuk ke `App\Models\User`. Untuk menggantinya:

```dotenv
KARYAWAN_USER_MODEL=App\Models\Admin
```

Atau di `config/karyawan.php` setelah di-publish:

```php
'user_model' => \App\Models\Admin::class,
```

### Menangani Exception

Package melempar exception yang dapat ditangkap di controller atau exception handler:

```php
use Aliziodev\LaravelKaryawanCore\Exceptions\EmployeeUserLinkException;
use Aliziodev\LaravelKaryawanCore\Exceptions\EmployeeCodeGenerationException;

class EmployeeUserController extends Controller
{
    public function __construct(
        private readonly LinkEmployeeUserAction $linkUserAction,
    ) {}

    public function store(LinkEmployeeUserRequest $request, Employee $employee): JsonResponse
    {
        try {
            $this->linkUserAction->execute($employee, (int) $request->user_id);
        } catch (EmployeeUserLinkException $e) {
            // Kemungkinan pesan:
            // - Karyawan sudah memiliki akun yang terhubung
            // - Akun user sudah terhubung ke karyawan lain
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json(['message' => 'Akun berhasil dikaitkan.']);
    }
}
```

Untuk penanganan global, daftarkan di `bootstrap/app.php`:

```php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (EmployeeUserLinkException $e) {
        return response()->json(['message' => $e->getMessage()], 409);
    });
})
```

---

## Pengujian

Package ini menggunakan [PestPHP](https://pestphp.com/) dengan database SQLite in-memory.

```bash
composer test
```

### Menggunakan Factory di Aplikasi Anda

```php
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Aliziodev\LaravelKaryawanCore\Models\Employee;

// Di TestCase::setUp()
Factory::guessFactoryNamesUsing(function (string $modelName) {
    return 'Aliziodev\\LaravelKaryawanCore\\Database\\Factories\\'
        . class_basename($modelName)
        . 'Factory';
});

// Di dalam test
$company  = Company::factory()->create(['is_active' => true]);
$employee = Employee::factory()->create(['company_id' => $company->id]);
```

### Konfigurasi TestCase untuk Package

```php
protected function getEnvironmentSetUp($app): void
{
    $app['config']->set('karyawan.routes.api.enabled', true);
    $app['config']->set('karyawan.routes.api.middleware', [
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ]);
}
```

> **Penting:** Route model binding memerlukan middleware `SubstituteBindings`. Tanpa middleware ini, model Eloquent tidak akan di-resolve dari parameter route saat pengujian.

---

## Lisensi

Package ini adalah perangkat lunak open-source yang dilisensikan di bawah [lisensi MIT](LICENSE).

---

<p align="center">Dibuat dengan ❤️ untuk komunitas Laravel Indonesia oleh <a href="https://github.com/aliziodev">Aliziodev</a></p>
