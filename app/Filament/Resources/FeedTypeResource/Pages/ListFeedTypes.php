<?php

namespace App\Filament\Resources\FeedTypeResource\Pages;

use App\Filament\Resources\FeedTypeResource;
use Filament\Resources\Pages\ListRecords;

class ListFeedTypes extends ListRecords
{
    protected static string $resource = FeedTypeResource::class;

    protected static ?string $title = 'Feed Types';
}