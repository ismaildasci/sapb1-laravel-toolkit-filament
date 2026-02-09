<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\PartnerResource\RelationManagers;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use SapB1\Toolkit\Enums\DocumentStatus;
use SapB1\Toolkit\Filament\Concerns\SapRelationManager;
use SapB1\Toolkit\Filament\Resources\OrderResource;
use SapB1\Toolkit\Models\Sales\Order;
use SapB1\Toolkit\Models\SapB1Model;

class OrdersRelationManager extends SapRelationManager
{
    protected static ?string $title = null;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return static::$title ?? __('sapb1-filament::resources.relation_managers.orders.title');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getRecords(
        ?string $search,
        ?string $sortColumn,
        ?string $sortDirection,
    ): array {
        /** @phpstan-ignore-next-line */
        $cardCode = $this->getOwnerRecord()->CardCode;

        $query = Order::where('CardCode', $cardCode);

        if (filled($sortColumn)) {
            $query->orderBy($sortColumn, $sortDirection ?? 'asc');
        } else {
            $query->orderBy('DocNum', 'desc');
        }

        $records = $query->get();

        $mapped = collect($records->map(fn (SapB1Model $order): array => array_merge(
            $order->toArray(),
            ['__key' => (string) $order->getKey()],
        )));

        if (filled($search)) {
            $searchLower = mb_strtolower($search);
            $mapped = $mapped->filter(fn (array $record): bool => str_contains(mb_strtolower((string) ($record['DocNum'] ?? '')), $searchLower)
                || str_contains(mb_strtolower((string) ($record['CardName'] ?? '')), $searchLower));
        }

        return $mapped->values()->all();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('DocNum')
                    ->label(__('sapb1-filament::resources.relation_managers.orders.columns.doc_num'))
                    ->weight('bold')
                    ->url(fn (array $record): string => OrderResource::getUrl('view', ['record' => $record['__key']]))
                    ->sortable(),

                Tables\Columns\TextColumn::make('CardName')
                    ->label(__('sapb1-filament::resources.relation_managers.orders.columns.card_name'))
                    ->wrap()
                    ->sortable(),

                Tables\Columns\TextColumn::make('DocDate')
                    ->label(__('sapb1-filament::resources.relation_managers.orders.columns.doc_date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('DocTotal')
                    ->label(__('sapb1-filament::resources.relation_managers.orders.columns.doc_total'))
                    ->money('TRY')
                    ->alignEnd()
                    ->sortable(),

                Tables\Columns\TextColumn::make('DocumentStatus')
                    ->label(__('sapb1-filament::resources.relation_managers.orders.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof DocumentStatus ? $state->label() : $state)
                    ->color(fn ($state): string => match (true) {
                        $state === DocumentStatus::Open || $state === 'bost_Open' => 'success',
                        $state === DocumentStatus::Closed || $state === 'bost_Close' => 'gray',
                        $state === DocumentStatus::Cancelled || $state === 'bost_Cancelled' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('filament-actions::view.single.label'))
                    ->icon('heroicon-m-eye')
                    ->url(fn (array $record): string => OrderResource::getUrl('view', ['record' => $record['__key']])),
            ])
            ->defaultSort('DocNum', 'desc')
            ->searchable();
    }
}
