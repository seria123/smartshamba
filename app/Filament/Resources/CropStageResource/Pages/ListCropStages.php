<?php

namespace App\Filament\Resources\CropStageResource\Pages;

use App\Filament\Resources\CropStageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCropStages extends ListRecords
{
    protected static string $resource = CropStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
