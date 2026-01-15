<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Widgets\SyncOverviewWidget;

it('can be instantiated', function () {
    $widget = new SyncOverviewWidget;

    expect($widget)->toBeInstanceOf(SyncOverviewWidget::class);
});

it('has polling interval set', function () {
    $widget = new SyncOverviewWidget;

    $reflection = new ReflectionClass($widget);
    $property = $reflection->getProperty('pollingInterval');

    expect($property->getValue($widget))->toBe('10s');
});

it('returns translated heading', function () {
    $widget = new SyncOverviewWidget;

    $heading = $widget->getHeading();

    expect($heading)->toBeString();
});
