<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Resources\AuditLogResource;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;

beforeEach(function () {
    // Register plugin for navigation group
    $plugin = SapB1FilamentPlugin::make();
    app()->instance(SapB1FilamentPlugin::class, $plugin);
});

it('has correct navigation icon', function () {
    $reflection = new ReflectionClass(AuditLogResource::class);
    $property = $reflection->getProperty('navigationIcon');

    expect($property->getValue())->toBe('heroicon-o-clipboard-document-list');
});

it('returns navigation label', function () {
    $label = AuditLogResource::getNavigationLabel();

    expect($label)->toBeString();
});

it('returns model label', function () {
    $label = AuditLogResource::getModelLabel();

    expect($label)->toBeString();
});

it('returns plural model label', function () {
    $label = AuditLogResource::getPluralModelLabel();

    expect($label)->toBeString();
});

it('has correct navigation sort', function () {
    $reflection = new ReflectionClass(AuditLogResource::class);
    $property = $reflection->getProperty('navigationSort');

    expect($property->getValue())->toBe(10);
});

it('has pages defined', function () {
    $pages = AuditLogResource::getPages();

    expect($pages)->toBeArray()
        ->toHaveKey('index')
        ->toHaveKey('view');
});
