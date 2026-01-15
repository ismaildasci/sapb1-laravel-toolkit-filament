<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Resources\TenantResource;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;

beforeEach(function () {
    // Register plugin for navigation group
    $plugin = SapB1FilamentPlugin::make();
    app()->instance(SapB1FilamentPlugin::class, $plugin);
});

it('has correct navigation icon', function () {
    $reflection = new ReflectionClass(TenantResource::class);
    $property = $reflection->getProperty('navigationIcon');

    expect($property->getValue())->toBe('heroicon-o-building-office-2');
});

it('returns navigation label', function () {
    $label = TenantResource::getNavigationLabel();

    expect($label)->toBeString();
});

it('returns model label', function () {
    $label = TenantResource::getModelLabel();

    expect($label)->toBeString();
});

it('returns plural model label', function () {
    $label = TenantResource::getPluralModelLabel();

    expect($label)->toBeString();
});

it('has correct navigation sort', function () {
    $reflection = new ReflectionClass(TenantResource::class);
    $property = $reflection->getProperty('navigationSort');

    expect($property->getValue())->toBe(30);
});

it('has pages defined', function () {
    $pages = TenantResource::getPages();

    expect($pages)->toBeArray()
        ->toHaveKey('index')
        ->toHaveKey('create');
});

it('returns tenant data from config', function () {
    config(['laravel-toolkit.multi_tenant.tenants' => [
        'tenant1' => [
            'name' => 'Test Tenant 1',
            'sap_url' => 'https://sap1.example.com',
            'sap_database' => 'SBOTEST1',
        ],
        'tenant2' => [
            'name' => 'Test Tenant 2',
            'sap_url' => 'https://sap2.example.com',
            'sap_database' => 'SBOTEST2',
        ],
    ]]);

    $data = TenantResource::getTenantData();

    expect($data)->toBeArray()
        ->toHaveCount(2);

    expect($data[0])->toMatchArray([
        'id' => 'tenant1',
        'name' => 'Test Tenant 1',
        'sap_database' => 'SBOTEST1',
    ]);
});

it('returns empty array when no tenants configured', function () {
    config(['laravel-toolkit.multi_tenant.tenants' => []]);

    $data = TenantResource::getTenantData();

    expect($data)->toBeArray()
        ->toBeEmpty();
});
