<?php

namespace App\Filament\Resources\CropCycleResource\Pages;

use App\Filament\Resources\CropCycleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCropCycle extends EditRecord
{
    protected static string $resource = CropCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
