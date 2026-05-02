<?php

namespace App\Filament\Resources\PlantingScheduleResource\Pages;

use App\Filament\Resources\PlantingScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlantingSchedule extends CreateRecord
{
    protected static string $resource = PlantingScheduleResource::class;

    protected function getRedirectUrl(): string
    {
        return \Illuminate\Support\Facades\Route::has('filament.admin.resources.planting-schedules.index')
            ? route('filament.admin.resources.planting-schedules.index')
            : PlantingScheduleResource::getUrl('index');
    }
}
