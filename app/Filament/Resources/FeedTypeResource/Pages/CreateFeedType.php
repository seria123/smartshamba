<?php

namespace App\Filament\Resources\FeedTypeResource\Pages;

use App\Filament\Resources\FeedTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeedType extends CreateRecord
{
    protected static string $resource = FeedTypeResource::class;

    protected static ?string $title = 'Create Feed Type';
}