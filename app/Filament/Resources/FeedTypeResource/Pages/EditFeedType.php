<?php

namespace App\Filament\Resources\FeedTypeResource\Pages;

use App\Filament\Resources\FeedTypeResource;
use Filament\Resources\Pages\EditRecord;

class EditFeedType extends EditRecord
{
    protected static string $resource = FeedTypeResource::class;

    protected static ?string $title = 'Edit Feed Type';
}