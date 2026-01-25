<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Resources\InvoiceResource;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\Models\Sales\Invoice;

beforeEach(function () {
    // Register plugin for navigation group
    $plugin = SapB1FilamentPlugin::make();
    app()->instance(SapB1FilamentPlugin::class, $plugin);
});

it('has correct model class', function () {
    $reflection = new ReflectionClass(InvoiceResource::class);
    $property = $reflection->getProperty('model');

    expect($property->getValue())->toBe(Invoice::class);
});

it('has correct navigation icon', function () {
    $reflection = new ReflectionClass(InvoiceResource::class);
    $property = $reflection->getProperty('navigationIcon');

    expect($property->getValue())->toBe('heroicon-o-document-text');
});

it('returns navigation label', function () {
    $label = InvoiceResource::getNavigationLabel();

    expect($label)->toBeString();
});

it('returns model label', function () {
    $label = InvoiceResource::getModelLabel();

    expect($label)->toBeString();
});

it('returns plural model label', function () {
    $label = InvoiceResource::getPluralModelLabel();

    expect($label)->toBeString();
});

it('has correct navigation sort', function () {
    $reflection = new ReflectionClass(InvoiceResource::class);
    $property = $reflection->getProperty('navigationSort');

    expect($property->getValue())->toBe(51);
});

it('has correct record title attribute', function () {
    $reflection = new ReflectionClass(InvoiceResource::class);
    $property = $reflection->getProperty('recordTitleAttribute');

    expect($property->getValue())->toBe('DocNum');
});

it('has all pages defined', function () {
    $pages = InvoiceResource::getPages();

    expect($pages)->toBeArray()
        ->toHaveKey('index')
        ->toHaveKey('create')
        ->toHaveKey('view')
        ->toHaveKey('edit');
});

it('has empty relations by default', function () {
    $relations = InvoiceResource::getRelations();

    expect($relations)->toBeArray()
        ->toBeEmpty();
});

it('can be enabled via plugin', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->invoiceEnabled(true);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isInvoiceEnabled())->toBeTrue();
});

it('can be disabled via plugin', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->invoiceEnabled(false);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isInvoiceEnabled())->toBeFalse();
});

it('is disabled when entities feature is disabled', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->entitiesEnabled(false)
        ->invoiceEnabled(true);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isInvoiceEnabled())->toBeFalse();
});
