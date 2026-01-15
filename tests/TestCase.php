<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SapB1\Toolkit\Filament\SapB1FilamentServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            SapB1FilamentServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
