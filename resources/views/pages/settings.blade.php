<x-filament-panels::page>
    <div class="space-y-6">
        {{-- General Settings --}}
        <x-filament::section>
            <x-slot name="heading">
                General Settings
            </x-slot>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Default Connection</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ config('laravel-toolkit.default_connection', 'default') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Event Dispatching</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if(config('laravel-toolkit.dispatch_events', true))
                            <x-filament::badge color="success">Enabled</x-filament::badge>
                        @else
                            <x-filament::badge color="gray">Disabled</x-filament::badge>
                        @endif
                    </dd>
                </div>
            </dl>
        </x-filament::section>

        {{-- Cache Configuration --}}
        @php $cacheConfig = $this->getCacheConfig(); @endphp
        <x-filament::section collapsible>
            <x-slot name="heading">
                Cache Configuration
            </x-slot>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($cacheConfig['enabled'])
                            <x-filament::badge color="success">Enabled</x-filament::badge>
                        @else
                            <x-filament::badge color="gray">Disabled</x-filament::badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Store</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $cacheConfig['store'] ?: 'default' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">TTL</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $cacheConfig['ttl'] }} seconds
                    </dd>
                </div>
            </dl>

            @if(!empty($cacheConfig['entities']))
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Entity Cache Settings</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Entity</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Enabled</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">TTL</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($cacheConfig['entities'] as $entity => $config)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $entity }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            @if($config['enabled'] ?? false)
                                                <x-filament::badge color="success" size="sm">Yes</x-filament::badge>
                                            @else
                                                <x-filament::badge color="gray" size="sm">No</x-filament::badge>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $config['ttl'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </x-filament::section>

        {{-- Sync Configuration --}}
        @php $syncConfig = $this->getSyncConfig(); @endphp
        <x-filament::section collapsible>
            <x-slot name="heading">
                Sync Configuration
            </x-slot>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($syncConfig['enabled'])
                            <x-filament::badge color="success">Enabled</x-filament::badge>
                        @else
                            <x-filament::badge color="gray">Disabled</x-filament::badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Batch Size</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ number_format($syncConfig['batch_size']) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Track Deletes</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($syncConfig['track_deletes'])
                            <x-filament::badge color="success">Yes</x-filament::badge>
                        @else
                            <x-filament::badge color="gray">No</x-filament::badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dispatch Events</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($syncConfig['dispatch_events'])
                            <x-filament::badge color="success">Yes</x-filament::badge>
                        @else
                            <x-filament::badge color="gray">No</x-filament::badge>
                        @endif
                    </dd>
                </div>
            </dl>
        </x-filament::section>

        {{-- Audit Configuration --}}
        @php $auditConfig = $this->getAuditConfig(); @endphp
        <x-filament::section collapsible>
            <x-slot name="heading">
                Audit Configuration
            </x-slot>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($auditConfig['enabled'])
                            <x-filament::badge color="success">Enabled</x-filament::badge>
                        @else
                            <x-filament::badge color="gray">Disabled</x-filament::badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Driver</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $auditConfig['driver'] }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Retention</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($auditConfig['retention_enabled'])
                            {{ $auditConfig['retention_days'] }} days
                        @else
                            <x-filament::badge color="gray">Disabled</x-filament::badge>
                        @endif
                    </dd>
                </div>
            </dl>
        </x-filament::section>

        {{-- Multi-Tenant Configuration --}}
        @php $mtConfig = $this->getMultiTenantConfig(); @endphp
        <x-filament::section collapsible>
            <x-slot name="heading">
                Multi-Tenant Configuration
            </x-slot>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($mtConfig['enabled'])
                            <x-filament::badge color="success">Enabled</x-filament::badge>
                        @else
                            <x-filament::badge color="gray">Disabled</x-filament::badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Resolver</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $mtConfig['resolver'] }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registered Tenants</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $mtConfig['tenants_count'] }}
                    </dd>
                </div>
            </dl>
        </x-filament::section>

        {{-- Environment Variables Reference --}}
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                Environment Variables Reference
            </x-slot>

            <div class="prose dark:prose-invert max-w-none">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Configuration can be customized using the following environment variables:
                </p>
                <ul class="text-sm space-y-1 mt-2">
                    <li><code>SAPB1_TOOLKIT_CACHE_ENABLED</code> - Enable/disable caching</li>
                    <li><code>SAPB1_TOOLKIT_CACHE_TTL</code> - Default cache TTL in seconds</li>
                    <li><code>SAPB1_TOOLKIT_SYNC_ENABLED</code> - Enable/disable sync</li>
                    <li><code>SAPB1_TOOLKIT_SYNC_BATCH_SIZE</code> - Sync batch size</li>
                    <li><code>SAPB1_TOOLKIT_AUDIT_ENABLED</code> - Enable/disable audit</li>
                    <li><code>SAPB1_TOOLKIT_AUDIT_DRIVER</code> - Audit driver (database, log, null)</li>
                    <li><code>SAPB1_TOOLKIT_MULTI_TENANT_ENABLED</code> - Enable/disable multi-tenant</li>
                </ul>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
