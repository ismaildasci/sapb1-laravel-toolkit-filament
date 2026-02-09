<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Concerns\SapRelationManager;
use SapB1\Toolkit\Filament\Resources\PartnerResource\RelationManagers\InvoicesRelationManager;

it('extends sap relation manager', function () {
    expect(is_subclass_of(InvoicesRelationManager::class, SapRelationManager::class))
        ->toBeTrue();
});

it('returns translated title', function () {
    $model = new class extends \Illuminate\Database\Eloquent\Model {};
    $title = InvoicesRelationManager::getTitle($model, \Filament\Resources\Pages\ViewRecord::class);

    expect($title)->toBeString()->not->toBeEmpty();
});

it('always allows viewing for record', function () {
    $model = new class extends \Illuminate\Database\Eloquent\Model {};

    expect(InvoicesRelationManager::canViewForRecord($model, \Filament\Resources\Pages\ViewRecord::class))
        ->toBeTrue();
});
