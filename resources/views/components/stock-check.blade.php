<div class="space-y-4 p-4">
    <div class="text-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $itemCode }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $itemName }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('sapb1-filament::resources.item.fields.quantity_on_stock') }}
            </p>
            <p @class([
                'text-2xl font-bold mt-1',
                'text-green-600 dark:text-green-400' => $onStock > 0,
                'text-red-600 dark:text-red-400' => $onStock <= 0,
            ])>
                {{ number_format((float) $onStock, 2) }}
            </p>
        </div>

        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('sapb1-filament::resources.item.fields.available') }}
            </p>
            @php
                $available = max(0, $onStock - $orderedByCustomers);
            @endphp
            <p @class([
                'text-2xl font-bold mt-1',
                'text-green-600 dark:text-green-400' => $available > 0,
                'text-orange-600 dark:text-orange-400' => $available > 0 && $available < $onStock * 0.2,
                'text-red-600 dark:text-red-400' => $available <= 0,
            ])>
                {{ number_format($available, 2) }}
            </p>
        </div>

        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('sapb1-filament::resources.item.fields.quantity_ordered_customers') }}
            </p>
            <p class="text-2xl font-bold mt-1 text-gray-900 dark:text-gray-100">
                {{ number_format((float) $orderedByCustomers, 2) }}
            </p>
        </div>

        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('sapb1-filament::resources.item.fields.quantity_ordered_vendors') }}
            </p>
            <p class="text-2xl font-bold mt-1 text-blue-600 dark:text-blue-400">
                {{ number_format((float) $orderedFromVendors, 2) }}
            </p>
        </div>
    </div>

    @if(is_array($stockLevel))
        <div class="mt-4">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('sapb1-filament::resources.item.actions.warehouse_breakdown') }}
            </h4>
            <div class="space-y-2">
                @foreach($stockLevel as $warehouse => $qty)
                    <div class="flex items-center justify-between p-2 rounded bg-gray-50 dark:bg-gray-800">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $warehouse }}</span>
                        <span @class([
                            'text-sm font-medium',
                            'text-green-600 dark:text-green-400' => $qty > 0,
                            'text-red-600 dark:text-red-400' => $qty <= 0,
                        ])>
                            {{ number_format((float) $qty, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
