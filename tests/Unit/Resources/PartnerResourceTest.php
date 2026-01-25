<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Resources\PartnerResource;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\Models\BusinessPartner\Partner;

beforeEach(function () {
    // Register plugin for navigation group
    $plugin = SapB1FilamentPlugin::make();
    app()->instance(SapB1FilamentPlugin::class, $plugin);
});

it('has correct model class', function () {
    $reflection = new ReflectionClass(PartnerResource::class);
    $property = $reflection->getProperty('model');

    expect($property->getValue())->toBe(Partner::class);
});

it('has correct navigation icon', function () {
    $reflection = new ReflectionClass(PartnerResource::class);
    $property = $reflection->getProperty('navigationIcon');

    expect($property->getValue())->toBe('heroicon-o-user-group');
});

it('returns navigation label', function () {
    $label = PartnerResource::getNavigationLabel();

    expect($label)->toBeString();
});

it('returns model label', function () {
    $label = PartnerResource::getModelLabel();

    expect($label)->toBeString();
});

it('returns plural model label', function () {
    $label = PartnerResource::getPluralModelLabel();

    expect($label)->toBeString();
});

it('has correct navigation sort', function () {
    $reflection = new ReflectionClass(PartnerResource::class);
    $property = $reflection->getProperty('navigationSort');

    expect($property->getValue())->toBe(40);
});

it('has correct record title attribute', function () {
    $reflection = new ReflectionClass(PartnerResource::class);
    $property = $reflection->getProperty('recordTitleAttribute');

    expect($property->getValue())->toBe('CardName');
});

it('has all pages defined', function () {
    $pages = PartnerResource::getPages();

    expect($pages)->toBeArray()
        ->toHaveKey('index')
        ->toHaveKey('create')
        ->toHaveKey('view')
        ->toHaveKey('edit');
});

it('has empty relations by default', function () {
    $relations = PartnerResource::getRelations();

    expect($relations)->toBeArray()
        ->toBeEmpty();
});

it('can be enabled via plugin', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->partnerEnabled(true);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isPartnerEnabled())->toBeTrue();
});

it('can be disabled via plugin', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->partnerEnabled(false);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isPartnerEnabled())->toBeFalse();
});

it('is disabled when entities feature is disabled', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->entitiesEnabled(false)
        ->partnerEnabled(true);

    app()->instance(SapB1FilamentPlugin::class, $plugin);

    expect($plugin->isPartnerEnabled())->toBeFalse();
});
