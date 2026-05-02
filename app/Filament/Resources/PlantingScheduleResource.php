<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlantingScheduleResource\Pages;
use App\Models\PlantingSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlantingScheduleResource extends Resource
{
    protected static ?string $model = PlantingSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Planting Schedule';

    protected static ?string $modelLabel = 'Planting';

    protected static ?string $pluralModelLabel = 'Planting Schedule';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('crop_id')
                            ->label('Crop')
                            ->relationship('crop', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('field_id')
                            ->label('Field / Plot')
                            ->relationship('field', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('farm_id')
                            ->label('Farm')
                            ->relationship('farm', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('variety')
                            ->label('Variety')
                            ->maxLength(255)
                            ->helperText('Specific variety if different from crop default'),
                        Forms\Components\Select::make('season')
                            ->options([
                                'spring' => 'Spring',
                                'summer' => 'Summer',
                                'fall' => 'Fall',
                                'winter' => 'Winter',
                                'year_round' => 'Year Round',
                            ])
                            ->default('spring')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Planting Schedule')
                    ->schema([
                        Forms\Components\DatePicker::make('planting_date')
                            ->label('Planting Date')
                            ->required()
                            ->native(false)
                            ->closeOnDateSelection(),
                        Forms\Components\DatePicker::make('planting_window_start')
                            ->label('Planting Window Start')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->helperText('Recommended earliest planting'),
                        Forms\Components\DatePicker::make('planting_window_end')
                            ->label('Planting Window End')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->helperText('Recommended latest planting'),
                        Forms\Components\DatePicker::make('expected_harvest_date')
                            ->label('Expected Harvest Date')
                            ->native(false)
                            ->closeOnDateSelection(),
                        Forms\Components\DatePicker::make('actual_harvest_date')
                            ->label('Actual Harvest Date')
                            ->native(false)
                            ->closeOnDateSelection(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Harvest Estimates')
                    ->schema([
                        Forms\Components\TextInput::make('estimated_quantity')
                            ->label('Estimated Yield')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\Select::make('quantity_unit')
                            ->options([
                                'kg' => 'Kilograms',
                                'ton' => 'Tonnes',
                                'lb' => 'Pounds',
                                'bushel' => 'Bushels',
                                'sack' => 'Sacks',
                                'basket' => 'Baskets',
                            ])
                            ->default('kg'),
                        Forms\Components\TextInput::make('actual_quantity')
                            ->label('Actual Harvested')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\Select::make('actual_quantity_unit')
                            ->options([
                                'kg' => 'Kilograms',
                                'ton' => 'Tonnes',
                                'lb' => 'Pounds',
                                'bushel' => 'Bushels',
                                'sack' => 'Sacks',
                                'basket' => 'Baskets',
                            ])
                            ->placeholder('Same as estimated'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Progress')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'planned' => 'Planned',
                                'planted' => 'Planted',
                                'growing' => 'Growing',
                                'ready_for_harvest' => 'Ready for Harvest',
                                'harvested' => 'Harvested',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('planned')
                            ->required(),
                        Forms\Components\Select::make('current_stage')
                            ->label('Growth Stage')
                            ->options([
                                'planning' => 'Planning',
                                'bed_preparation' => 'Bed Preparation',
                                'sowing' => 'Sowing/Planting',
                                'germination' => 'Germination',
                                'seedling' => 'Seedling',
                                'vegetative' => 'Vegetative Growth',
                                'flowering' => 'Flowering',
                                'fruiting' => 'Fruiting',
                                'ripening' => 'Ripening',
                                'harvest_ready' => 'Harvest Ready',
                            ])
                            ->searchable(),
                        Forms\Components\Slider::make('completion_percentage')
                            ->label('Progress (%)')
                            ->min(0)
                            ->max(100)
                            ->default(0)
                            ->step(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Links')
                    ->schema([
                        Forms\Components\Select::make('crop_cycle_id')
                            ->label('Linked Crop Cycle')
                            ->relationship('cropCycle', 'id')
                            ->helperText('Optional: link to existing crop cycle')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('crop.name')
                    ->label('Crop')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('field.name')
                    ->label('Field')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('planting_date')
                    ->label('Planted')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_harvest_date')
                    ->label('Harvest By')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'planned' => 'gray',
                        'planted' => 'blue',
                        'growing' => 'green',
                        'ready_for_harvest' => 'yellow',
                        'harvested' => 'emerald',
                        'cancelled' => 'red',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('season')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('variety')
                    ->searchable(),
                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label('Progress')
                    ->badge()
                    ->color(fn (int $state): string => match(true) {
                        $state >= 100 => 'success',
                        $state >= 75 => 'warning',
                        $state >= 50 => 'primary',
                        $state >= 25 => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('days_until_planting')
                    ->label('In')
                    ->badge()
                    ->color(fn (int $state): string => match(true) {
                        $state < 0 => 'danger',
                        $state === 0 => 'warning',
                        $state <= 7 => 'success',
                        $state <= 30 => 'info',
                        default => 'gray',
                    })
                    ->getStateUsing(fn (PlantingSchedule $record) => $record->days_until_planting),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planned' => 'Planned',
                        'planted' => 'Planted',
                        'growing' => 'Growing',
                        'ready_for_harvest' => 'Ready for Harvest',
                        'harvested' => 'Harvested',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('season')
                    ->options([
                        'spring' => 'Spring',
                        'summer' => 'Summer',
                        'fall' => 'Fall',
                        'winter' => 'Winter',
                        'year_round' => 'Year Round',
                    ]),
                Tables\Filters\SelectFilter::make('crop_id')
                    ->relationship('crop', 'name')
                    ->label('Crop'),
                Tables\Filters\SelectFilter::make('field_id')
                    ->relationship('field', 'name')
                    ->label('Field'),
                Tables\Filters\SelectFilter::make('farm_id')
                    ->relationship('farm', 'name')
                    ->label('Farm'),
                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming Plantings')
                    ->query(fn (Builder $query): Builder => $query->where('planting_date', '>=', now())),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue')
                    ->query(fn (Builder $query): Builder => $query->where('planting_date', '<', now())->where('status', 'planned'))
                    ->indicator('Overdue plantings'),
                Tables\Filters\Filter::make('active')
                    ->label('Active Growing')
                    ->query(fn (Builder $query): Builder => $query->whereIn('status', ['planted', 'growing', 'ready_for_harvest']))
                    ->indicator('Currently growing'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('planting_date', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlantingSchedules::route('/'),
            'create' => Pages\CreatePlantingSchedule::route('/create'),
            'edit' => Pages\EditPlantingSchedule::route('/{record}/edit'),
        ];
    }
}
