<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Widgets\SystemHealthWidget;

it('can be instantiated', function () {
    $widget = new SystemHealthWidget;

    expect($widget)->toBeInstanceOf(SystemHealthWidget::class);
});

it('has polling interval set', function () {
    $widget = new SystemHealthWidget;

    $reflection = new ReflectionClass($widget);
    $property = $reflection->getProperty('pollingInterval');

    expect($property->getValue($widget))->toBe('30s');
});

it('has full column span', function () {
    $widget = new SystemHealthWidget;

    $reflection = new ReflectionClass($widget);
    $property = $reflection->getProperty('columnSpan');

    expect($property->getValue($widget))->toBe('full');
});
