<?php

namespace App\Filament\Resources\FeedTypeResource\Pages;

use App\Filament\Resources\FeedTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFeedType extends ViewRecord
{
    protected static string $resource = FeedTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
