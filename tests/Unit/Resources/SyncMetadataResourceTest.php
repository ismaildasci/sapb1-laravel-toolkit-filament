<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Resources\SyncMetadataResource;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;

beforeEach(function () {
    // Register plugin for navigation group
    $plugin = SapB1FilamentPlugin::make();
    app()->instance(SapB1FilamentPlugin::class, $plugin);
});

it('has correct navigation icon', function () {
    $reflection = new ReflectionClass(SyncMetadataResource::class);
    $property = $reflection->getProperty('navigationIcon');

    expect($property->getValue())->toBe('heroicon-o-arrow-path');
});

it('returns navigation label', function () {
    $label = SyncMetadataResource::getNavigationLabel();

    expect($label)->toBeString();
});

it('returns model label', function () {
    $label = SyncMetadataResource::getModelLabel();

    expect($label)->toBeString();
});

it('returns plural model label', function () {
    $label = SyncMetadataResource::getPluralModelLabel();

    expect($label)->toBeString();
});

it('has correct navigation sort', function () {
    $reflection = new ReflectionClass(SyncMetadataResource::class);
    $property = $reflection->getProperty('navigationSort');

    expect($property->getValue())->toBe(20);
});

it('has pages defined', function () {
    $pages = SyncMetadataResource::getPages();

    expect($pages)->toBeArray()
        ->toHaveKey('index');
});
