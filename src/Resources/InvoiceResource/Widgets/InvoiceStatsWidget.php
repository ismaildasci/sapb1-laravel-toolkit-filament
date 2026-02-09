<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\InvoiceResource\Widgets;

use Exception;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use SapB1\Toolkit\Models\Sales\Invoice;

class InvoiceStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $stats = Cache::remember('sapb1_invoice_stats', 300, function (): array {
            try {
                $firstOfMonth = now()->startOfMonth()->format('Y-m-d');
                $today = now()->format('Y-m-d');

                $totalThisMonth = Invoice::query()
                    ->where('DocDate', '>=', $firstOfMonth)
                    ->count();

                $invoicesThisMonth = Invoice::query()
                    ->where('DocDate', '>=', $firstOfMonth)
                    ->select(['DocTotal'])
                    ->get();

                $totalRevenue = (float) $invoicesThisMonth->sum('DocTotal');

                $openInvoices = Invoice::query()
                    ->where('DocumentStatus', 'bost_Open')
                    ->select(['DocTotal', 'PaidToDate'])
                    ->get();

                $outstanding = 0.0;

                foreach ($openInvoices as $inv) {
                    $outstanding += (float) ($inv->DocTotal ?? 0) - (float) ($inv->PaidToDate ?? 0);
                }

                $overdueCount = Invoice::query()
                    ->where('DocDueDate', '<', $today)
                    ->where('DocumentStatus', 'bost_Open')
                    ->count();

                return [
                    'total' => $totalThisMonth,
                    'revenue' => $totalRevenue,
                    'outstanding' => $outstanding,
                    'overdue' => $overdueCount,
                ];
            } catch (Exception) {
                return ['total' => 0, 'revenue' => 0, 'outstanding' => 0, 'overdue' => 0];
            }
        });

        return [
            Stat::make(
                __('sapb1-filament::resources.invoice.widgets.total_this_month'),
                (string) $stats['total'],
            )->icon('heroicon-o-document-text'),

            Stat::make(
                __('sapb1-filament::resources.invoice.widgets.total_revenue'),
                number_format((float) $stats['revenue'], 2).' TRY',
            )->icon('heroicon-o-banknotes'),

            Stat::make(
                __('sapb1-filament::resources.invoice.widgets.outstanding'),
                number_format((float) $stats['outstanding'], 2).' TRY',
            )
                ->icon('heroicon-o-exclamation-triangle')
                ->color($stats['outstanding'] > 0 ? 'danger' : 'success'),

            Stat::make(
                __('sapb1-filament::resources.invoice.widgets.overdue'),
                (string) $stats['overdue'],
            )
                ->icon('heroicon-o-clock')
                ->color($stats['overdue'] > 0 ? 'danger' : 'success'),
        ];
    }
}
