<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Support;

use Exception;
use SapB1\Health\SapB1HealthCheck;

class HealthChecker
{
    /**
     * Check overall system health.
     *
     * @return array<string, mixed>
     */
    public function check(): array
    {
        return [
            'sap_connection' => $this->checkSapConnection(),
            'audit_system' => $this->checkAuditSystem(),
            'sync_system' => $this->checkSyncSystem(),
            'cache_system' => $this->checkCacheSystem(),
            'multi_tenant' => $this->checkMultiTenant(),
        ];
    }

    /**
     * Check SAP B1 connection status.
     *
     * @return array<string, mixed>
     */
    public function checkSapConnection(): array
    {
        try {
            $healthCheck = app(SapB1HealthCheck::class);
            $result = $healthCheck->check();

            return [
                'status' => $result->healthy ? 'healthy' : 'unhealthy',
                'message' => $result->message,
                'latency' => $result->responseTime !== null ? (int) round($result->responseTime) : null,
                'last_check' => now()->toIso8601String(),
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'latency' => null,
                'last_check' => now()->toIso8601String(),
            ];
        }
    }

    /**
     * Check audit system status.
     *
     * @return array<string, mixed>
     */
    public function checkAuditSystem(): array
    {
        $enabled = (bool) config('laravel-toolkit.audit.enabled', true);
        $driver = config('laravel-toolkit.audit.driver', 'database');

        return [
            'status' => $enabled ? 'enabled' : 'disabled',
            'driver' => $driver,
            'retention_days' => config('laravel-toolkit.audit.retention.days', 365),
        ];
    }

    /**
     * Check sync system status.
     *
     * @return array<string, mixed>
     */
    public function checkSyncSystem(): array
    {
        $enabled = (bool) config('laravel-toolkit.sync.enabled', true);
        $entities = config('laravel-toolkit.sync.entities', []);

        return [
            'status' => $enabled ? 'enabled' : 'disabled',
            'configured_entities' => count($entities),
            'batch_size' => config('laravel-toolkit.sync.batch_size', 5000),
        ];
    }

    /**
     * Check cache system status.
     *
     * @return array<string, mixed>
     */
    public function checkCacheSystem(): array
    {
        $enabled = (bool) config('laravel-toolkit.cache.enabled', false);
        $store = config('laravel-toolkit.cache.store', 'default');

        return [
            'status' => $enabled ? 'enabled' : 'disabled',
            'store' => $store,
            'ttl' => config('laravel-toolkit.cache.ttl', 3600),
        ];
    }

    /**
     * Check multi-tenant status.
     *
     * @return array<string, mixed>
     */
    public function checkMultiTenant(): array
    {
        $enabled = (bool) config('laravel-toolkit.multi_tenant.enabled', false);
        $tenants = config('laravel-toolkit.multi_tenant.tenants', []);

        return [
            'status' => $enabled ? 'enabled' : 'disabled',
            'resolver' => config('laravel-toolkit.multi_tenant.resolver', 'config'),
            'tenants_count' => count($tenants),
        ];
    }

    /**
     * Get a summary status for the dashboard.
     */
    public function getSummaryStatus(): string
    {
        $health = $this->check();

        if ($health['sap_connection']['status'] === 'error') {
            return 'critical';
        }

        if ($health['sap_connection']['status'] === 'unhealthy') {
            return 'warning';
        }

        return 'healthy';
    }
}
