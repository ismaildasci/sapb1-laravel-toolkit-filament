<div class="space-y-4 p-4">
    @if(empty($flow))
        <p class="text-gray-500 text-sm">{{ __('sapb1-filament::resources.common.actions.no_document_flow') }}</p>
    @else
        <div class="space-y-3">
            @foreach($flow as $document)
                <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                        @switch($document['type'] ?? $document['ObjectType'] ?? '')
                            @case('Orders')
                            @case('17')
                                <x-heroicon-o-shopping-cart class="w-5 h-5 text-blue-500" />
                                @break
                            @case('DeliveryNotes')
                            @case('15')
                                <x-heroicon-o-truck class="w-5 h-5 text-indigo-500" />
                                @break
                            @case('Invoices')
                            @case('13')
                                <x-heroicon-o-document-text class="w-5 h-5 text-green-500" />
                                @break
                            @case('CreditNotes')
                            @case('14')
                                <x-heroicon-o-receipt-refund class="w-5 h-5 text-orange-500" />
                                @break
                            @default
                                <x-heroicon-o-document class="w-5 h-5 text-gray-500" />
                        @endswitch
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $document['type'] ?? $document['ObjectType'] ?? 'Document' }}
                            #{{ $document['DocNum'] ?? $document['DocEntry'] ?? '-' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $document['DocDate'] ?? '' }}
                            @if(isset($document['DocTotal']))
                                &middot; {{ number_format((float) $document['DocTotal'], 2) }} TRY
                            @endif
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        @php
                            $status = $document['DocumentStatus'] ?? $document['status'] ?? '';
                        @endphp
                        <span @class([
                            'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $status === 'bost_Open' || $status === 'Open',
                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' => $status === 'bost_Close' || $status === 'Closed',
                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' => $status === 'bost_Cancelled' || $status === 'Cancelled',
                        ])>
                            {{ $status }}
                        </span>
                    </div>
                </div>
                @if(!$loop->last)
                    <div class="flex justify-center">
                        <x-heroicon-o-arrow-down class="w-4 h-4 text-gray-400" />
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
