<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Actions;

use Exception;
use Filament\Notifications\Notification;
use SapB1\Health\SapB1HealthCheck;

class TestConnectionAction
{
    public function __construct(
        protected SapB1HealthCheck $healthCheck
    ) {}

    /**
     * Test SAP B1 connection for default connection.
     */
    public function test(?string $connection = null): bool
    {
        try {
            $result = $this->healthCheck->check($connection);

            if ($result->healthy) {
                Notification::make()
                    ->success()
                    ->title(__('sapb1-filament::resources.tenant.notifications.test_success'))
                    ->body($result->message)
                    ->send();

                return true;
            }

            Notification::make()
                ->danger()
                ->title(__('sapb1-filament::resources.tenant.notifications.test_failed'))
                ->body($result->message)
                ->send();

            return false;
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('sapb1-filament::resources.tenant.notifications.test_failed'))
                ->body($e->getMessage())
                ->send();

            return false;
        }
    }

    /**
     * Test all configured connections.
     *
     * @return array<string, bool>
     */
    public function testAll(): array
    {
        $results = [];

        foreach ($this->healthCheck->checkAll() as $connection => $result) {
            $results[$connection] = $result->healthy;
        }

        $healthy = array_filter($results);
        $total = count($results);
        $healthyCount = count($healthy);

        if ($healthyCount === $total) {
            Notification::make()
                ->success()
                ->title('All connections healthy')
                ->body("{$healthyCount}/{$total} connections are healthy")
                ->send();
        } else {
            Notification::make()
                ->warning()
                ->title('Some connections failed')
                ->body("{$healthyCount}/{$total} connections are healthy")
                ->send();
        }

        return $results;
    }
}
