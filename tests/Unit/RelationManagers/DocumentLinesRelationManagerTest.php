<?php

declare(strict_types=1);

use SapB1\Toolkit\Filament\Concerns\SapRelationManager;
use SapB1\Toolkit\Filament\Resources\Concerns\DocumentLinesRelationManager;

it('extends sap relation manager', function () {
    expect(is_subclass_of(DocumentLinesRelationManager::class, SapRelationManager::class))
        ->toBeTrue();
});

it('returns translated title', function () {
    $model = new class extends \Illuminate\Database\Eloquent\Model {};
    $title = DocumentLinesRelationManager::getTitle($model, \Filament\Resources\Pages\ViewRecord::class);

    expect($title)->toBeString()->not->toBeEmpty();
});

it('always allows viewing for record', function () {
    $model = new class extends \Illuminate\Database\Eloquent\Model {};

    expect(DocumentLinesRelationManager::canViewForRecord($model, \Filament\Resources\Pages\ViewRecord::class))
        ->toBeTrue();
});
