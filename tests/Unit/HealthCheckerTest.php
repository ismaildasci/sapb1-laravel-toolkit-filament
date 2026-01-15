<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Support\HealthChecker;

beforeEach(function () {
    $this->healthChecker = new HealthChecker;
});

it('returns health check array', function () {
    $result = $this->healthChecker->check();

    expect($result)->toBeArray()
        ->toHaveKeys(['sap_connection', 'audit_system', 'sync_system', 'cache_system', 'multi_tenant']);
});

it('checks audit system status', function () {
    config(['laravel-toolkit.audit.enabled' => true]);
    config(['laravel-toolkit.audit.driver' => 'database']);

    $result = $this->healthChecker->checkAuditSystem();

    expect($result)->toBeArray()
        ->toHaveKey('status')
        ->toHaveKey('driver');
});

it('checks sync system status', function () {
    config(['laravel-toolkit.sync.enabled' => true]);
    config(['laravel-toolkit.sync.batch_size' => 5000]);

    $result = $this->healthChecker->checkSyncSystem();

    expect($result)->toBeArray()
        ->toHaveKey('status')
        ->toHaveKey('batch_size');
});

it('checks cache system status', function () {
    config(['laravel-toolkit.cache.enabled' => false]);

    $result = $this->healthChecker->checkCacheSystem();

    expect($result)->toBeArray()
        ->toHaveKey('status')
        ->and($result['status'])->toBe('disabled');
});

it('checks multi tenant status', function () {
    config(['laravel-toolkit.multi_tenant.enabled' => false]);

    $result = $this->healthChecker->checkMultiTenant();

    expect($result)->toBeArray()
        ->toHaveKey('status')
        ->and($result['status'])->toBe('disabled');
});

it('returns summary status', function () {
    $result = $this->healthChecker->getSummaryStatus();

    expect($result)->toBeString()
        ->toBeIn(['healthy', 'warning', 'critical']);
});
