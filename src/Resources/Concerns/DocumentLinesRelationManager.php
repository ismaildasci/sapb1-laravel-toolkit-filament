<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources\Concerns;

use Filament\Tables;
use Filament\Tables\Table;
use SapB1\Toolkit\Filament\Concerns\SapRelationManager;

class DocumentLinesRelationManager extends SapRelationManager
{
    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return static::$title ?? __('sapb1-filament::resources.relation_managers.document_lines.title');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getRecords(
        ?string $search,
        ?string $sortColumn,
        ?string $sortDirection,
    ): array {
        /** @var array<int, array<string, mixed>> $lines */
        $lines = $this->getOwnerRecord()->DocumentLines ?? [];

        return array_map(function (array $line, int $index): array {
            $line['__key'] = (string) ($line['LineNum'] ?? $index);

            return $line;
        }, $lines, array_keys($lines));
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('LineNum')
                    ->label(__('sapb1-filament::resources.relation_managers.document_lines.columns.line_num'))
                    ->sortable(false),

                Tables\Columns\TextColumn::make('ItemCode')
                    ->label(__('sapb1-filament::resources.relation_managers.document_lines.columns.item_code'))
                    ->weight('bold')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('ItemDescription')
                    ->label(__('sapb1-filament::resources.relation_managers.document_lines.columns.item_description'))
                    ->wrap()
                    ->sortable(false),

                Tables\Columns\TextColumn::make('Quantity')
                    ->label(__('sapb1-filament::resources.relation_managers.document_lines.columns.quantity'))
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd()
                    ->sortable(false),

                Tables\Columns\TextColumn::make('Price')
                    ->label(__('sapb1-filament::resources.relation_managers.document_lines.columns.price'))
                    ->money('TRY')
                    ->alignEnd()
                    ->sortable(false),

                Tables\Columns\TextColumn::make('DiscountPercent')
                    ->label(__('sapb1-filament::resources.relation_managers.document_lines.columns.discount_percent'))
                    ->suffix('%')
                    ->alignEnd()
                    ->sortable(false),

                Tables\Columns\TextColumn::make('WarehouseCode')
                    ->label(__('sapb1-filament::resources.relation_managers.document_lines.columns.warehouse_code'))
                    ->sortable(false),

                Tables\Columns\TextColumn::make('LineTotal')
                    ->label(__('sapb1-filament::resources.relation_managers.document_lines.columns.line_total'))
                    ->money('TRY')
                    ->alignEnd()
                    ->weight('bold')
                    ->sortable(false),
            ])
            ->paginated(false);
    }
}
