<?php

namespace App\Filament\Pages;

use App\Models\PlantingSchedule;
use Filament\Pages\Page;
use Filament\Actions;

class PlantingCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationLabel = 'Crop Calendar';
    
    protected static ?string $title = 'Crop Calendar';
    
    protected static ?string $navigationGroup = 'Planning';
    
    protected static ?int $navigationSort = 2;
    
    protected static string $view = 'filament.pages.planting-calendar';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_list')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => route('filament.admin.resources.planting-schedules.index'))
                ->color('gray'),
            Actions\Action::make('create_new')
                ->label('New Planting')
                ->icon('heroicon-o-plus')
                ->url(fn (): string => route('filament.admin.resources.planting-schedules.create'))
                ->color('success'),
        ];
    }
    
    public function getViewData(): array
    {
        $schedules = PlantingSchedule::with(['crop', 'field', 'farm'])
            ->orderBy('planting_date', 'asc')
            ->get();
        
        return [
            'schedules' => $schedules,
        ];
    }
}
