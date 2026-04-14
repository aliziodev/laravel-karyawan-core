<?php

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeEmergencyContact;

beforeEach(function () {
    $this->employee = Employee::factory()->create();
});

it('GET /emergency-contacts returns contacts', function () {
    $this->employee->emergencyContacts()->create([
        'name' => 'Kontak Satu', 'relationship' => 'Istri', 'phone' => '081',
    ]);

    $response = $this->getJson("/api/karyawan/employees/{$this->employee->id}/emergency-contacts");

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Kontak Satu');
});

it('POST /emergency-contacts creates contact', function () {
    $response = $this->postJson("/api/karyawan/employees/{$this->employee->id}/emergency-contacts", [
        'name' => 'Reva Fatma',
        'relationship' => 'Istri',
        'phone' => '08123456789',
        'is_primary' => true,
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.name', 'Reva Fatma');
    $response->assertJsonPath('data.is_primary', true);
});

it('POST /emergency-contacts validates required fields', function () {
    $response = $this->postJson("/api/karyawan/employees/{$this->employee->id}/emergency-contacts", []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['name', 'relationship', 'phone']);
});

it('PUT /emergency-contacts/{id} updates contact', function () {
    $contact = $this->employee->emergencyContacts()->create([
        'name' => 'Nama Lama', 'relationship' => 'Istri', 'phone' => '081',
    ]);

    $response = $this->putJson(
        "/api/karyawan/employees/{$this->employee->id}/emergency-contacts/{$contact->id}",
        ['name' => 'Nama Baru', 'relationship' => 'Istri', 'phone' => '089']
    );

    $response->assertOk();
    $response->assertJsonPath('data.name', 'Nama Baru');
});

it('PUT /emergency-contacts/{id} returns 404 for wrong employee', function () {
    $otherEmployee = Employee::factory()->create();
    $contact = $otherEmployee->emergencyContacts()->create([
        'name' => 'Kontak Orang Lain', 'relationship' => 'Istri', 'phone' => '081',
    ]);

    $response = $this->putJson(
        "/api/karyawan/employees/{$this->employee->id}/emergency-contacts/{$contact->id}",
        ['name' => 'Diubah', 'relationship' => 'Orang Tua', 'phone' => '082']
    );

    $response->assertNotFound();
});

it('DELETE /emergency-contacts/{id} deletes contact', function () {
    $contact = $this->employee->emergencyContacts()->create([
        'name' => 'Kontak Hapus', 'relationship' => 'Saudara', 'phone' => '081',
    ]);

    $contactId = $contact->id;

    $response = $this->deleteJson(
        "/api/karyawan/employees/{$this->employee->id}/emergency-contacts/{$contactId}"
    );

    $response->assertOk();
    expect(EmployeeEmergencyContact::find($contactId))->toBeNull();
});
