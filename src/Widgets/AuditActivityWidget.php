<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use SapB1\Toolkit\Models\AuditLog;

class AuditActivityWidget extends BaseWidget
{
    protected static ?string $heading = null;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];

    protected static ?string $pollingInterval = null;

    public function getHeading(): ?string
    {
        return __('sapb1-filament::widgets.audit.heading');
    }

    protected function getTableQuery(): Builder
    {
        return AuditLog::query()
            ->latest()
            ->limit(config('sapb1-filament.widgets.audit_activity.limit', 10));
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('entity_type')
                ->label(__('sapb1-filament::widgets.audit.entity'))
                ->searchable()
                ->sortable(),

            TextColumn::make('entity_id')
                ->label(__('sapb1-filament::widgets.audit.id'))
                ->searchable(),

            BadgeColumn::make('event')
                ->label(__('sapb1-filament::widgets.audit.event'))
                ->colors([
                    'success' => 'created',
                    'warning' => 'updated',
                    'danger' => 'deleted',
                    'info' => fn ($state): bool => ! in_array($state, ['created', 'updated', 'deleted']),
                ]),

            TextColumn::make('user_id')
                ->label(__('sapb1-filament::widgets.audit.user'))
                ->placeholder('-'),

            TextColumn::make('created_at')
                ->label(__('sapb1-filament::widgets.audit.time'))
                ->since()
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view')
                ->icon('heroicon-o-eye')
                ->url(fn (AuditLog $record): string => route('filament.admin.resources.audit-logs.view', $record)),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
