<?php

use Aliziodev\LaravelKaryawanCore\Models\Company;

it('GET /api/karyawan/companies returns paginated list', function () {
    Company::factory()->count(3)->create();

    $response = $this->getJson('/api/karyawan/companies');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [['id', 'code', 'name', 'is_active']],
        'meta' => ['total'],
    ]);
});

it('POST /api/karyawan/companies creates company', function () {
    $response = $this->postJson('/api/karyawan/companies', [
        'code' => 'PT001',
        'name' => 'PT Test Indonesia',
        'is_active' => true,
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.name', 'PT Test Indonesia');
    $response->assertJsonPath('data.code', 'PT001');
});

it('GET /api/karyawan/companies/{id} returns company with branches', function () {
    $company = Company::factory()->create(['name' => 'PT Example']);

    $response = $this->getJson("/api/karyawan/companies/{$company->id}");

    $response->assertOk();
    $response->assertJsonPath('data.name', 'PT Example');
    $response->assertJsonStructure(['data' => ['branches', 'departments', 'positions']]);
});

it('PUT /api/karyawan/companies/{id} updates company', function () {
    $company = Company::factory()->create(['name' => 'Nama Lama']);

    $response = $this->putJson("/api/karyawan/companies/{$company->id}", [
        'code' => $company->code,
        'name' => 'Nama Baru',
        'is_active' => true,
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.name', 'Nama Baru');
});

it('DELETE /api/karyawan/companies/{id} deletes company', function () {
    $company = Company::factory()->create();

    $response = $this->deleteJson("/api/karyawan/companies/{$company->id}");

    $response->assertOk();
    $response->assertJsonPath('message', 'Perusahaan berhasil dihapus.');
    expect(Company::find($company->id))->toBeNull();
});

it('GET /companies supports active_only filter', function () {
    Company::factory()->create(['is_active' => true]);
    Company::factory()->create(['is_active' => false]);

    $response = $this->getJson('/api/karyawan/companies?active_only=1');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});
