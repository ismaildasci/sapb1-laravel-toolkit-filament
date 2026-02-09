<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Resources\OrderResource;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\Models\Sales\Order;

beforeEach(function () {
    // Register plugin for navigation group
    $plugin = SapB1FilamentPlugin::make();
    app()->instance(SapB1FilamentPlugin::class, $plugin);
});

it('has correct model class', function () {
    $reflection = new ReflectionClass(OrderResource::class);
    $property = $reflection->getProperty('model');

    expect($property->getValue())->toBe(Order::class);
});

it('has correct navigation icon', function () {
    $reflection = new ReflectionClass(OrderResource::class);
    $property = $reflection->getProperty('navigationIcon');

    expect($property->getValue())->toBe('heroicon-o-shopping-cart');
});

it('returns navigation label', function () {
    $label = OrderResource::getNavigationLabel();

    expect($label)->toBeString();
});

it('returns model label', function () {
    $label = OrderResource::getModelLabel();

    expect($label)->toBeString();
});

it('returns plural model label', function () {
    $label = OrderResource::getPluralModelLabel();

    expect($label)->toBeString();
});

it('has correct navigation sort', function () {
    $reflection = new ReflectionClass(OrderResource::class);
    $property = $reflection->getProperty('navigationSort');

    expect($property->getValue())->toBe(50);
});

it('has correct record title attribute', function () {
    $reflection = new ReflectionClass(OrderResource::class);
    $property = $reflection->getProperty('recordTitleAttribute');

    expect($property->getValue())->toBe('DocNum');
});

it('has all pages defined', function () {
    $pages = OrderResource::getPages();

    expect($pages)->toBeArray()
        ->toHaveKey('index')
        ->toHaveKey('create')
        ->toHaveKey('view')
        ->toHaveKey('edit');
});

it('has document lines relation manager', function () {
    $relations = OrderResource::getRelations();

    expect($relations)->toBeArray()
        ->toContain(\SapB1\Toolkit\Filament\Resources\Concerns\DocumentLinesRelationManager::class);
});

it('can be enabled via plugin', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->orderEnabled(true);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isOrderEnabled())->toBeTrue();
});

it('can be disabled via plugin', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->orderEnabled(false);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isOrderEnabled())->toBeFalse();
});

it('is disabled when entities feature is disabled', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->entitiesEnabled(false)
        ->orderEnabled(true);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isOrderEnabled())->toBeFalse();
});
