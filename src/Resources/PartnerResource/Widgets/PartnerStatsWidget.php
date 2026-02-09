<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\PartnerResource\Widgets;

use Exception;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use SapB1\Toolkit\Models\BusinessPartner\Partner;

class PartnerStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $stats = Cache::remember('sapb1_partner_stats', 300, function (): array {
            try {
                $total = Partner::query()->count();
                $customers = Partner::query()->where('CardType', 'cCustomer')->count();
                $suppliers = Partner::query()->where('CardType', 'cSupplier')->count();

                $withBalance = Partner::query()
                    ->where('CurrentAccountBalance', '>', 0)
                    ->select(['CurrentAccountBalance'])
                    ->get();

                $outstandingBalance = (float) $withBalance->sum('CurrentAccountBalance');

                return [
                    'total' => $total,
                    'customers' => $customers,
                    'suppliers' => $suppliers,
                    'outstanding' => $outstandingBalance,
                ];
            } catch (Exception) {
                return ['total' => 0, 'customers' => 0, 'suppliers' => 0, 'outstanding' => 0];
            }
        });

        return [
            Stat::make(
                __('sapb1-filament::resources.partner.widgets.total'),
                (string) $stats['total'],
            )->icon('heroicon-o-user-group'),

            Stat::make(
                __('sapb1-filament::resources.partner.widgets.customers'),
                (string) $stats['customers'],
            )
                ->icon('heroicon-o-user')
                ->color('success'),

            Stat::make(
                __('sapb1-filament::resources.partner.widgets.suppliers'),
                (string) $stats['suppliers'],
            )
                ->icon('heroicon-o-building-storefront')
                ->color('info'),

            Stat::make(
                __('sapb1-filament::resources.partner.widgets.outstanding_balance'),
                number_format((float) $stats['outstanding'], 2).' TRY',
            )
                ->icon('heroicon-o-banknotes')
                ->color($stats['outstanding'] > 0 ? 'danger' : 'success'),
        ];
    }
}
