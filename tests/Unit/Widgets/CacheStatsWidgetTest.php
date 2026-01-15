<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Widgets\CacheStatsWidget;

it('can be instantiated', function () {
    $widget = new CacheStatsWidget;

    expect($widget)->toBeInstanceOf(CacheStatsWidget::class);
});

it('has no polling interval', function () {
    $widget = new CacheStatsWidget;

    $reflection = new ReflectionClass($widget);
    $property = $reflection->getProperty('pollingInterval');

    expect($property->getValue($widget))->toBeNull();
});

it('formats ttl correctly for days', function () {
    $widget = new CacheStatsWidget;

    $reflection = new ReflectionClass($widget);
    $method = $reflection->getMethod('formatTtl');

    expect($method->invoke($widget, 86400))->toBe('1 day');
    expect($method->invoke($widget, 172800))->toBe('2 days');
});

it('formats ttl correctly for hours', function () {
    $widget = new CacheStatsWidget;

    $reflection = new ReflectionClass($widget);
    $method = $reflection->getMethod('formatTtl');

    expect($method->invoke($widget, 3600))->toBe('1 hour');
    expect($method->invoke($widget, 7200))->toBe('2 hours');
});

it('formats ttl correctly for minutes', function () {
    $widget = new CacheStatsWidget;

    $reflection = new ReflectionClass($widget);
    $method = $reflection->getMethod('formatTtl');

    expect($method->invoke($widget, 60))->toBe('1 minute');
    expect($method->invoke($widget, 120))->toBe('2 minutes');
});

it('formats ttl correctly for seconds', function () {
    $widget = new CacheStatsWidget;

    $reflection = new ReflectionClass($widget);
    $method = $reflection->getMethod('formatTtl');

    expect($method->invoke($widget, 30))->toBe('30 seconds');
});
