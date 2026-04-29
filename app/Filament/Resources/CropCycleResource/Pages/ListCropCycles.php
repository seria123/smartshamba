<?php

namespace App\Filament\Resources\CropCycleResource\Pages;

use App\Filament\Resources\CropCycleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCropCycles extends ListRecords
{
    protected static string $resource = CropCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
