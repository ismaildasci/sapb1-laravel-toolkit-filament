<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use SapB1\Toolkit\Enums\ItemType;
use SapB1\Toolkit\Filament\Actions\CheckStockAction;
use SapB1\Toolkit\Filament\Actions\UploadAttachmentAction;
use SapB1\Toolkit\Filament\Resources\ItemResource\Pages;
use SapB1\Toolkit\Filament\Resources\ItemResource\Widgets;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\Models\Inventory\Item;
use SapB1\Toolkit\Services\BatchService;

class ItemResource extends Resource
{
    /** @phpstan-ignore-next-line */
    protected static ?string $model = Item::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 50;

    protected static ?string $recordTitleAttribute = 'ItemName';

    public static function getNavigationLabel(): string
    {
        return __('sapb1-filament::resources.item.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return SapB1FilamentPlugin::get()->getNavigationGroup();
    }

    public static function getModelLabel(): string
    {
        return __('sapb1-filament::resources.item.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sapb1-filament::resources.item.plural_model_label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return SapB1FilamentPlugin::get()->isItemEnabled();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('sapb1-filament::resources.item.sections.basic'))
                    ->schema([
                        TextInput::make('ItemCode')
                            ->label(__('sapb1-filament::resources.item.fields.item_code'))
                            ->required()
                            ->maxLength(50)
                            ->disabled(fn ($record) => $record !== null),

                        TextInput::make('ItemName')
                            ->label(__('sapb1-filament::resources.item.fields.item_name'))
                            ->required()
                            ->maxLength(100),

                        TextInput::make('ForeignName')
                            ->label(__('sapb1-filament::resources.item.fields.foreign_name'))
                            ->maxLength(100),

                        TextInput::make('BarCode')
                            ->label(__('sapb1-filament::resources.item.fields.barcode'))
                            ->maxLength(254),

                        Select::make('ItemType')
                            ->label(__('sapb1-filament::resources.item.fields.item_type'))
                            ->options([
                                ItemType::Items->value => ItemType::Items->label(),
                                ItemType::Labor->value => ItemType::Labor->label(),
                                ItemType::Travel->value => ItemType::Travel->label(),
                                ItemType::FixedAssets->value => ItemType::FixedAssets->label(),
                            ])
                            ->default(ItemType::Items->value),
                    ])
                    ->columns(2),

                Section::make(__('sapb1-filament::resources.item.sections.category'))
                    ->schema([
                        TextInput::make('ItemsGroupCode')
                            ->label(__('sapb1-filament::resources.item.fields.group_code'))
                            ->numeric(),

                        TextInput::make('Manufacturer')
                            ->label(__('sapb1-filament::resources.item.fields.manufacturer'))
                            ->maxLength(50),

                        TextInput::make('DefaultWarehouse')
                            ->label(__('sapb1-filament::resources.item.fields.default_warehouse'))
                            ->maxLength(8),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make(__('sapb1-filament::resources.item.sections.sales_purchase'))
                    ->schema([
                        Toggle::make('SalesItem')
                            ->label(__('sapb1-filament::resources.item.fields.sales_item'))
                            ->default(true),

                        Toggle::make('PurchaseItem')
                            ->label(__('sapb1-filament::resources.item.fields.purchase_item'))
                            ->default(true),

                        Toggle::make('InventoryItem')
                            ->label(__('sapb1-filament::resources.item.fields.inventory_item'))
                            ->default(true),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make(__('sapb1-filament::resources.item.sections.stock'))
                    ->schema([
                        TextInput::make('QuantityOnStock')
                            ->label(__('sapb1-filament::resources.item.fields.quantity_on_stock'))
                            ->numeric()
                            ->disabled(),

                        TextInput::make('QuantityOrderedFromVendors')
                            ->label(__('sapb1-filament::resources.item.fields.quantity_ordered_vendors'))
                            ->numeric()
                            ->disabled(),

                        TextInput::make('QuantityOrderedByCustomers')
                            ->label(__('sapb1-filament::resources.item.fields.quantity_ordered_customers'))
                            ->numeric()
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->visibleOn('view'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('item_details')
                    ->tabs([
                        Tabs\Tab::make(__('sapb1-filament::resources.item.infolist.details'))
                            ->schema([
                                TextEntry::make('ItemCode')
                                    ->label(__('sapb1-filament::resources.item.fields.item_code'))
                                    ->weight('bold')
                                    ->copyable(),

                                TextEntry::make('ItemName')
                                    ->label(__('sapb1-filament::resources.item.fields.item_name')),

                                TextEntry::make('ForeignName')
                                    ->label(__('sapb1-filament::resources.item.fields.foreign_name'))
                                    ->placeholder('-'),

                                TextEntry::make('BarCode')
                                    ->label(__('sapb1-filament::resources.item.fields.barcode'))
                                    ->copyable()
                                    ->placeholder('-'),

                                TextEntry::make('ItemType')
                                    ->label(__('sapb1-filament::resources.item.fields.item_type'))
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state instanceof ItemType ? $state->label() : $state)
                                    ->color(fn ($state): string => match (true) {
                                        $state === ItemType::Items || $state === 'itItems' => 'success',
                                        $state === ItemType::Labor || $state === 'itLabor' => 'info',
                                        $state === ItemType::Travel || $state === 'itTravel' => 'warning',
                                        $state === ItemType::FixedAssets || $state === 'itFixedAssets' => 'primary',
                                        default => 'gray',
                                    }),

                                IconEntry::make('Valid')
                                    ->label(__('sapb1-filament::resources.item.fields.valid'))
                                    ->boolean(),

                                IconEntry::make('SalesItem')
                                    ->label(__('sapb1-filament::resources.item.fields.sales_item'))
                                    ->boolean(),

                                IconEntry::make('PurchaseItem')
                                    ->label(__('sapb1-filament::resources.item.fields.purchase_item'))
                                    ->boolean(),

                                IconEntry::make('InventoryItem')
                                    ->label(__('sapb1-filament::resources.item.fields.inventory_item'))
                                    ->boolean(),
                            ])
                            ->columns(3),

                        Tabs\Tab::make(__('sapb1-filament::resources.item.infolist.stock'))
                            ->schema([
                                TextEntry::make('QuantityOnStock')
                                    ->label(__('sapb1-filament::resources.item.fields.quantity_on_stock'))
                                    ->numeric(decimalPlaces: 2)
                                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                                TextEntry::make('available_quantity')
                                    ->label(__('sapb1-filament::resources.item.fields.available'))
                                    ->getStateUsing(fn ($record) => max(0, ($record->QuantityOnStock ?? 0) - ($record->QuantityOrderedByCustomers ?? 0)))
                                    ->numeric(decimalPlaces: 2)
                                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                                TextEntry::make('QuantityOrderedByCustomers')
                                    ->label(__('sapb1-filament::resources.item.fields.quantity_ordered_customers'))
                                    ->numeric(decimalPlaces: 2),

                                TextEntry::make('QuantityOrderedFromVendors')
                                    ->label(__('sapb1-filament::resources.item.fields.quantity_ordered_vendors'))
                                    ->numeric(decimalPlaces: 2),

                                TextEntry::make('DefaultWarehouse')
                                    ->label(__('sapb1-filament::resources.item.fields.default_warehouse'))
                                    ->placeholder('-'),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ItemCode')
                    ->label(__('sapb1-filament::resources.item.fields.item_code'))
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('ItemName')
                    ->label(__('sapb1-filament::resources.item.fields.item_name'))
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('BarCode')
                    ->label(__('sapb1-filament::resources.item.fields.barcode'))
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ItemType')
                    ->label(__('sapb1-filament::resources.item.fields.item_type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ItemType ? $state->label() : $state)
                    ->color(fn ($state): string => match (true) {
                        $state === ItemType::Items || $state === 'itItems' => 'success',
                        $state === ItemType::Labor || $state === 'itLabor' => 'info',
                        $state === ItemType::Travel || $state === 'itTravel' => 'warning',
                        $state === ItemType::FixedAssets || $state === 'itFixedAssets' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('QuantityOnStock')
                    ->label(__('sapb1-filament::resources.item.fields.quantity_on_stock'))
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('available_quantity')
                    ->label(__('sapb1-filament::resources.item.fields.available'))
                    ->getStateUsing(function ($record) {
                        $onStock = $record->QuantityOnStock ?? 0;
                        $ordered = $record->QuantityOrderedByCustomers ?? 0;

                        return max(0, $onStock - $ordered);
                    })
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                Tables\Columns\IconColumn::make('SalesItem')
                    ->label(__('sapb1-filament::resources.item.fields.sales_item'))
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('PurchaseItem')
                    ->label(__('sapb1-filament::resources.item.fields.purchase_item'))
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('Valid')
                    ->label(__('sapb1-filament::resources.item.fields.valid'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ItemType')
                    ->label(__('sapb1-filament::resources.item.filters.item_type'))
                    ->options([
                        ItemType::Items->value => ItemType::Items->label(),
                        ItemType::Labor->value => ItemType::Labor->label(),
                        ItemType::Travel->value => ItemType::Travel->label(),
                        ItemType::FixedAssets->value => ItemType::FixedAssets->label(),
                    ]),

                Tables\Filters\TernaryFilter::make('Valid')
                    ->label(__('sapb1-filament::resources.item.filters.valid'))
                    ->placeholder(__('sapb1-filament::resources.item.filters.all'))
                    ->trueLabel(__('sapb1-filament::resources.item.filters.active'))
                    ->falseLabel(__('sapb1-filament::resources.item.filters.inactive')),

                Tables\Filters\TernaryFilter::make('SalesItem')
                    ->label(__('sapb1-filament::resources.item.filters.sales_item')),

                Tables\Filters\TernaryFilter::make('PurchaseItem')
                    ->label(__('sapb1-filament::resources.item.filters.purchase_item')),

                Tables\Filters\TernaryFilter::make('InventoryItem')
                    ->label(__('sapb1-filament::resources.item.filters.inventory_item')),

                Tables\Filters\Filter::make('has_stock')
                    ->label(__('sapb1-filament::resources.item.filters.has_stock'))
                    ->query(fn ($query) => $query->where('QuantityOnStock', '>', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                CheckStockAction::make(),
                UploadAttachmentAction::make()
                    ->entityEndpoint('Items')
                    ->entityKeyField('ItemCode'),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_activate')
                        ->label(__('sapb1-filament::resources.item.actions.bulk_activate'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            /** @var BatchService $service */
                            $service = app(BatchService::class);
                            $updates = $records->map(fn ($record) => [
                                'ItemCode' => $record->ItemCode,
                                'Valid' => 'tYES',
                            ])->toArray();
                            $service->updateItems($updates);

                            Notification::make()
                                ->success()
                                ->title(__('sapb1-filament::resources.item.notifications.bulk_activate_complete'))
                                ->body(__('sapb1-filament::resources.item.notifications.bulk_action_count', [
                                    'count' => count($updates),
                                ]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('bulk_deactivate')
                        ->label(__('sapb1-filament::resources.item.actions.bulk_deactivate'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            /** @var BatchService $service */
                            $service = app(BatchService::class);
                            $updates = $records->map(fn ($record) => [
                                'ItemCode' => $record->ItemCode,
                                'Valid' => 'tNO',
                            ])->toArray();
                            $service->updateItems($updates);

                            Notification::make()
                                ->success()
                                ->title(__('sapb1-filament::resources.item.notifications.bulk_deactivate_complete'))
                                ->body(__('sapb1-filament::resources.item.notifications.bulk_action_count', [
                                    'count' => count($updates),
                                ]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('ItemCode', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\ItemStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'view' => Pages\ViewItem::route('/{record}'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
