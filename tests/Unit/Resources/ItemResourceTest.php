<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Resources\ItemResource;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\Models\Inventory\Item;

beforeEach(function () {
    // Register plugin for navigation group
    $plugin = SapB1FilamentPlugin::make();
    app()->instance(SapB1FilamentPlugin::class, $plugin);
});

it('has correct model class', function () {
    $reflection = new ReflectionClass(ItemResource::class);
    $property = $reflection->getProperty('model');

    expect($property->getValue())->toBe(Item::class);
});

it('has correct navigation icon', function () {
    $reflection = new ReflectionClass(ItemResource::class);
    $property = $reflection->getProperty('navigationIcon');

    expect($property->getValue())->toBe('heroicon-o-cube');
});

it('returns navigation label', function () {
    $label = ItemResource::getNavigationLabel();

    expect($label)->toBeString();
});

it('returns model label', function () {
    $label = ItemResource::getModelLabel();

    expect($label)->toBeString();
});

it('returns plural model label', function () {
    $label = ItemResource::getPluralModelLabel();

    expect($label)->toBeString();
});

it('has correct navigation sort', function () {
    $reflection = new ReflectionClass(ItemResource::class);
    $property = $reflection->getProperty('navigationSort');

    expect($property->getValue())->toBe(50);
});

it('has correct record title attribute', function () {
    $reflection = new ReflectionClass(ItemResource::class);
    $property = $reflection->getProperty('recordTitleAttribute');

    expect($property->getValue())->toBe('ItemName');
});

it('has all pages defined', function () {
    $pages = ItemResource::getPages();

    expect($pages)->toBeArray()
        ->toHaveKey('index')
        ->toHaveKey('create')
        ->toHaveKey('view')
        ->toHaveKey('edit');
});

it('has empty relations by default', function () {
    $relations = ItemResource::getRelations();

    expect($relations)->toBeArray()
        ->toBeEmpty();
});

it('can be enabled via plugin', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->itemEnabled(true);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isItemEnabled())->toBeTrue();
});

it('can be disabled via plugin', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->itemEnabled(false);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isItemEnabled())->toBeFalse();
});

it('is disabled when entities feature is disabled', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->entitiesEnabled(false)
        ->itemEnabled(true);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isItemEnabled())->toBeFalse();
});
