<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\SapB1FilamentPlugin;

it('can be instantiated', function () {
    $plugin = SapB1FilamentPlugin::make();

    expect($plugin)->toBeInstanceOf(SapB1FilamentPlugin::class);
});

it('has correct id', function () {
    $plugin = SapB1FilamentPlugin::make();

    expect($plugin->getId())->toBe('sapb1-toolkit');
});

it('has default navigation group', function () {
    $plugin = SapB1FilamentPlugin::make();

    expect($plugin->getNavigationGroup())->toBe('SAP B1');
});

it('allows setting navigation group', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->navigationGroup('Custom Group');

    expect($plugin->getNavigationGroup())->toBe('Custom Group');
});

it('has all features enabled by default', function () {
    $plugin = SapB1FilamentPlugin::make();

    expect($plugin->isDashboardEnabled())->toBeTrue()
        ->and($plugin->isAuditEnabled())->toBeTrue()
        ->and($plugin->isSyncEnabled())->toBeTrue()
        ->and($plugin->isCacheEnabled())->toBeTrue()
        ->and($plugin->isMultiTenantEnabled())->toBeTrue()
        ->and($plugin->isChangeTrackingEnabled())->toBeTrue()
        ->and($plugin->isSettingsEnabled())->toBeTrue();
});

it('allows disabling features', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->auditEnabled(false)
        ->syncEnabled(false)
        ->multiTenantEnabled(false);

    expect($plugin->isAuditEnabled())->toBeFalse()
        ->and($plugin->isSyncEnabled())->toBeFalse()
        ->and($plugin->isMultiTenantEnabled())->toBeFalse();
});

it('allows setting navigation sort', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->navigationSort(50);

    expect($plugin->getNavigationSort())->toBe(50);
});

it('allows setting navigation icon', function () {
    $plugin = SapB1FilamentPlugin::make()
        ->navigationIcon('heroicon-o-cog');

    expect($plugin->getNavigationIcon())->toBe('heroicon-o-cog');
});
