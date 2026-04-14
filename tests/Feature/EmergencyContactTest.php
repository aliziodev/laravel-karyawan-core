<?php

use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\EmployeeEmergencyContact;

beforeEach(function () {
    $this->employee = Employee::factory()->create();
});

it('can create emergency contact for employee', function () {
    $contact = $this->employee->emergencyContacts()->create([
        'name' => 'Reva Fatma',
        'relationship' => 'Istri',
        'phone' => '08123456789',
    ]);

    expect($contact)->toBeInstanceOf(EmployeeEmergencyContact::class);
    expect($contact->employee_id)->toBe($this->employee->id);
    expect($contact->name)->toBe('Reva Fatma');
    expect($contact->relationship)->toBe('Istri');
});

it('employee can have multiple emergency contacts', function () {
    $this->employee->emergencyContacts()->createMany([
        ['name' => 'Kontak Satu', 'relationship' => 'Istri', 'phone' => '081'],
        ['name' => 'Kontak Dua', 'relationship' => 'Orang Tua', 'phone' => '082'],
    ]);

    expect($this->employee->emergencyContacts()->count())->toBe(2);
});

it('primary contact is marked correctly', function () {
    $primary = $this->employee->emergencyContacts()->create([
        'name' => 'Kontak Utama',
        'relationship' => 'Istri',
        'phone' => '081',
        'is_primary' => true,
    ]);

    $secondary = $this->employee->emergencyContacts()->create([
        'name' => 'Kontak Kedua',
        'relationship' => 'Orang Tua',
        'phone' => '082',
        'is_primary' => false,
    ]);

    expect($primary->is_primary)->toBeTrue();
    expect($secondary->is_primary)->toBeFalse();
});

it('can update emergency contact', function () {
    $contact = $this->employee->emergencyContacts()->create([
        'name' => 'Nama Lama',
        'relationship' => 'Istri',
        'phone' => '081',
    ]);

    $contact->update(['name' => 'Nama Baru', 'phone' => '089']);

    expect($contact->fresh()->name)->toBe('Nama Baru');
    expect($contact->fresh()->phone)->toBe('089');
});

it('can delete emergency contact', function () {
    $contact = $this->employee->emergencyContacts()->create([
        'name' => 'Kontak Hapus',
        'relationship' => 'Saudara',
        'phone' => '081',
    ]);

    $contactId = $contact->id;
    $contact->delete();

    expect(EmployeeEmergencyContact::find($contactId))->toBeNull();
    expect($this->employee->emergencyContacts()->count())->toBe(0);
});

it('scope primary returns only primary contacts', function () {
    $this->employee->emergencyContacts()->create(['name' => 'Utama', 'relationship' => 'Istri', 'phone' => '081', 'is_primary' => true]);
    $this->employee->emergencyContacts()->create(['name' => 'Kedua', 'relationship' => 'Orang Tua', 'phone' => '082', 'is_primary' => false]);

    $primaryContacts = $this->employee->emergencyContacts()->primary()->get();

    expect($primaryContacts)->toHaveCount(1);
    expect($primaryContacts->first()->name)->toBe('Utama');
});

it('contact belongs to employee', function () {
    $contact = $this->employee->emergencyContacts()->create([
        'name' => 'Kontak Test',
        'relationship' => 'Saudara',
        'phone' => '081',
    ]);

    expect($contact->employee->id)->toBe($this->employee->id);
});
