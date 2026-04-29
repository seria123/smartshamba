<?php

namespace App\Filament\Resources\CropStageResource\Pages;

use App\Filament\Resources\CropStageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCropStage extends EditRecord
{
    protected static string $resource = CropStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
