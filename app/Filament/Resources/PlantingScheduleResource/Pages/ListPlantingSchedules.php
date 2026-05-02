<?php

namespace App\Filament\Resources\PlantingScheduleResource\Pages;

use App\Filament\Resources\PlantingScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlantingSchedules extends ListRecords
{
    protected static string $resource = PlantingScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
