<?php

use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Enums\DocumentType;
use Aliziodev\LaravelKaryawanCore\Enums\EmploymentType;
use Aliziodev\LaravelKaryawanCore\Enums\Gender;
use Aliziodev\LaravelKaryawanCore\Enums\HistoryType;
use Aliziodev\LaravelKaryawanCore\Enums\MaritalStatus;
use Aliziodev\LaravelKaryawanCore\Enums\Religion;

it('Gender enum has correct values and labels', function () {
    expect(Gender::Male->value)->toBe('male');
    expect(Gender::Female->value)->toBe('female');
    expect(Gender::Male->label())->toBe('Laki-laki');
    expect(Gender::Female->label())->toBe('Perempuan');
});

it('ActiveStatus::isWorking returns correct boolean', function () {
    expect(ActiveStatus::Active->isWorking())->toBeTrue();
    expect(ActiveStatus::LongLeave->isWorking())->toBeTrue();
    expect(ActiveStatus::Resigned->isWorking())->toBeFalse();
    expect(ActiveStatus::Terminated->isWorking())->toBeFalse();
    expect(ActiveStatus::Retired->isWorking())->toBeFalse();
    expect(ActiveStatus::Inactive->isWorking())->toBeFalse();
});

it('all enums can be created from their string values', function () {
    expect(Gender::from('male'))->toBe(Gender::Male);
    expect(MaritalStatus::from('married'))->toBe(MaritalStatus::Married);
    expect(Religion::from('islam'))->toBe(Religion::Islam);
    expect(EmploymentType::from('permanent'))->toBe(EmploymentType::Permanent);
    expect(ActiveStatus::from('active'))->toBe(ActiveStatus::Active);
    expect(DocumentType::from('ktp'))->toBe(DocumentType::Ktp);
    expect(HistoryType::from('status_change'))->toBe(HistoryType::StatusChange);
});

it('all enums have label method returning non-empty string', function () {
    foreach (Gender::cases() as $case) {
        expect($case->label())->toBeString()->not()->toBeEmpty();
    }
    foreach (ActiveStatus::cases() as $case) {
        expect($case->label())->toBeString()->not()->toBeEmpty();
    }
    foreach (EmploymentType::cases() as $case) {
        expect($case->label())->toBeString()->not()->toBeEmpty();
    }
    foreach (DocumentType::cases() as $case) {
        expect($case->label())->toBeString()->not()->toBeEmpty();
    }
});
