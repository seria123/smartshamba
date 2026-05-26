<?php

namespace App\Filament\Resources\FeedTypeResource\Pages;

use App\Filament\Resources\FeedTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeedType extends EditRecord
{
    protected static string $resource = FeedTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn ($record) => $record->id !== auth()->id()),
        ];
    }
}