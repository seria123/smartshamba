<?php

namespace App\Filament\Resources\FeedTypeResource\Pages;

use App\Filament\Resources\FeedTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeedTypes extends ListRecords
{
    protected static string $resource = FeedTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
