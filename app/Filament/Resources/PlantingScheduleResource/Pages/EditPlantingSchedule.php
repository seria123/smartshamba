<?php

namespace App\Filament\Resources\PlantingScheduleResource\Pages;

use App\Filament\Resources\PlantingScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlantingSchedule extends EditRecord
{
    protected static string $resource = PlantingScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return \Illuminate\Support\Facades\Route::has('filament.admin.resources.planting-schedules.index')
            ? route('filament.admin.resources.planting-schedules.index')
            : PlantingScheduleResource::getUrl('index');
    }
}
