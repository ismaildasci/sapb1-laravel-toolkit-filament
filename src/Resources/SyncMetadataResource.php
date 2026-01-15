<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Resources;

use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SapB1\Toolkit\Filament\Resources\SyncMetadataResource\Pages;
use SapB1\Toolkit\Filament\SapB1FilamentPlugin;
use SapB1\Toolkit\Sync\LocalSyncService;
use SapB1\Toolkit\Sync\SyncMetadata;

class SyncMetadataResource extends Resource
{
    protected static ?string $model = SyncMetadata::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'entity';

    public static function getNavigationLabel(): string
    {
        return __('sapb1-filament::resources.sync_metadata.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return SapB1FilamentPlugin::get()->getNavigationGroup();
    }

    public static function getModelLabel(): string
    {
        return __('sapb1-filament::resources.sync_metadata.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sapb1-filament::resources.sync_metadata.plural_model_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('sapb1-filament::resources.sync_metadata.sections.info'))
                    ->schema([
                        Forms\Components\TextInput::make('entity')
                            ->label(__('sapb1-filament::resources.sync_metadata.fields.entity'))
                            ->disabled(),

                        Forms\Components\TextInput::make('table_name')
                            ->label(__('sapb1-filament::resources.sync_metadata.fields.table_name'))
                            ->disabled(),

                        Forms\Components\TextInput::make('status')
                            ->label(__('sapb1-filament::resources.sync_metadata.fields.status'))
                            ->disabled(),

                        Forms\Components\TextInput::make('synced_count')
                            ->label(__('sapb1-filament::resources.sync_metadata.fields.synced_count'))
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('last_synced_at')
                            ->label(__('sapb1-filament::resources.sync_metadata.fields.last_synced_at'))
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('last_full_sync_at')
                            ->label(__('sapb1-filament::resources.sync_metadata.fields.last_full_sync_at'))
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('sapb1-filament::resources.sync_metadata.sections.errors'))
                    ->schema([
                        Forms\Components\Textarea::make('last_error')
                            ->label(__('sapb1-filament::resources.sync_metadata.fields.last_error'))
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => $record?->last_error === null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity')
                    ->label(__('sapb1-filament::resources.sync_metadata.fields.entity'))
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('table_name')
                    ->label(__('sapb1-filament::resources.sync_metadata.fields.table_name'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('sapb1-filament::resources.sync_metadata.fields.status'))
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'running' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('synced_count')
                    ->label(__('sapb1-filament::resources.sync_metadata.fields.synced_count'))
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('last_synced_at')
                    ->label(__('sapb1-filament::resources.sync_metadata.fields.last_synced_at'))
                    ->sortable()
                    ->since()
                    ->placeholder(__('sapb1-filament::resources.sync_metadata.never')),

                Tables\Columns\IconColumn::make('has_error')
                    ->label(__('sapb1-filament::resources.sync_metadata.fields.has_error'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->last_error !== null)
                    ->trueIcon('heroicon-o-exclamation-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('sapb1-filament::resources.sync_metadata.filters.status'))
                    ->options([
                        'pending' => 'Pending',
                        'running' => 'Running',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('sync')
                    ->label(__('sapb1-filament::resources.sync_metadata.actions.sync'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(fn (SyncMetadata $record) => static::triggerSync($record)),

                Tables\Actions\Action::make('full_sync')
                    ->label(__('sapb1-filament::resources.sync_metadata.actions.full_sync'))
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription(__('sapb1-filament::resources.sync_metadata.full_sync_warning'))
                    ->action(fn (SyncMetadata $record) => static::triggerFullSync($record)),

                Tables\Actions\Action::make('reset')
                    ->label(__('sapb1-filament::resources.sync_metadata.actions.reset'))
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription(__('sapb1-filament::resources.sync_metadata.reset_warning'))
                    ->action(fn (SyncMetadata $record) => static::resetMetadata($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_sync')
                        ->label(__('sapb1-filament::resources.sync_metadata.actions.bulk_sync'))
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->action(fn ($records) => static::bulkSync($records)),
                ]),
            ])
            ->defaultSort('entity', 'asc')
            ->poll(config('sapb1-filament.sync.poll_interval', 10).'s');
    }

    protected static function triggerSync(SyncMetadata $record): void
    {
        try {
            $syncService = app(LocalSyncService::class);
            $result = $syncService->sync($record->entity);

            Notification::make()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.sync_success'))
                ->body(sprintf(
                    'Created: %d, Updated: %d, Duration: %.2fs',
                    $result->created,
                    $result->updated,
                    $result->duration
                ))
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.sync_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected static function triggerFullSync(SyncMetadata $record): void
    {
        try {
            $syncService = app(LocalSyncService::class);
            $result = $syncService->fullSyncWithDeletes($record->entity);

            Notification::make()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.full_sync_success'))
                ->body(sprintf(
                    'Created: %d, Updated: %d, Deleted: %d, Duration: %.2fs',
                    $result->created,
                    $result->updated,
                    $result->deleted,
                    $result->duration
                ))
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title(__('sapb1-filament::resources.sync_metadata.notifications.sync_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected static function resetMetadata(SyncMetadata $record): void
    {
        $record->update([
            'status' => 'pending',
            'last_synced_at' => null,
            'last_full_sync_at' => null,
            'synced_count' => 0,
            'last_error' => null,
        ]);

        Notification::make()
            ->title(__('sapb1-filament::resources.sync_metadata.notifications.reset_success'))
            ->success()
            ->send();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, SyncMetadata>  $records
     */
    protected static function bulkSync($records): void
    {
        $syncService = app(LocalSyncService::class);
        $successCount = 0;
        $failCount = 0;

        foreach ($records as $record) {
            try {
                $syncService->sync($record->entity);
                $successCount++;
            } catch (Exception $e) {
                $failCount++;
            }
        }

        Notification::make()
            ->title(__('sapb1-filament::resources.sync_metadata.notifications.bulk_sync_complete'))
            ->body(sprintf('Success: %d, Failed: %d', $successCount, $failCount))
            ->success()
            ->send();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSyncMetadata::route('/'),
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

    public static function canDelete($record): bool
    {
        return false;
    }
}
