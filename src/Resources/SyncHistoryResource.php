<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SapB1\Toolkit\Filament\Models\SyncHistory;
use SapB1\Toolkit\Filament\Resources\SyncHistoryResource\Pages;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;

class SyncHistoryResource extends Resource
{
    protected static ?string $model = SyncHistory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 25;

    public static function getNavigationLabel(): string
    {
        return __('sapb1-filament::resources.sync_history.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return SapB1FilamentPlugin::get()->getNavigationGroup();
    }

    public static function getModelLabel(): string
    {
        return __('sapb1-filament::resources.sync_history.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sapb1-filament::resources.sync_history.plural_model_label');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity')
                    ->label(__('sapb1-filament::resources.sync_history.fields.entity'))
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('sync_type')
                    ->label(__('sapb1-filament::resources.sync_history.fields.sync_type'))
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'incremental' => 'Incremental',
                        'full' => 'Full',
                        'full_with_deletes' => 'Full + Deletes',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'incremental' => 'gray',
                        'full' => 'warning',
                        'full_with_deletes' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('sapb1-filament::resources.sync_history.fields.status'))
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'running' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('records_synced')
                    ->label(__('sapb1-filament::resources.sync_history.fields.records_synced'))
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('duration')
                    ->label(__('sapb1-filament::resources.sync_history.fields.duration'))
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('duration_ms', $direction)),

                Tables\Columns\TextColumn::make('started_at')
                    ->label(__('sapb1-filament::resources.sync_history.fields.started_at'))
                    ->dateTime()
                    ->sortable()
                    ->since(),

                Tables\Columns\TextColumn::make('error_message')
                    ->label(__('sapb1-filament::resources.sync_history.fields.error'))
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->error_message)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('entity')
                    ->label(__('sapb1-filament::resources.sync_history.filters.entity'))
                    ->options(fn () => SyncHistory::query()
                        ->distinct()
                        ->pluck('entity', 'entity')
                        ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('status')
                    ->label(__('sapb1-filament::resources.sync_history.filters.status'))
                    ->options([
                        'running' => 'Running',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),

                Tables\Filters\SelectFilter::make('sync_type')
                    ->label(__('sapb1-filament::resources.sync_history.filters.sync_type'))
                    ->options([
                        'incremental' => 'Incremental',
                        'full' => 'Full',
                        'full_with_deletes' => 'Full + Deletes',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('started_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSyncHistory::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }
}
