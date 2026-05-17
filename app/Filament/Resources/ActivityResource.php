<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  protected static ?string $navigationGroup = 'Farm Operations';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Activity Details')
                    ->schema([
                        Forms\Components\Select::make('activity_type')
                            ->label('Activity Type')
                            ->options(Activity::getActivityTypes())
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('activity_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('crop_stage_id')
                            ->relationship('cropStage', 'stage_name')
                            ->label('Crop Stage')
                            ->required(),
                        Forms\Components\Select::make('field_id')
                            ->label('Field')
                            ->relationship('field', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('activity_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(3),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Personnel')
                    ->schema([
                        Forms\Components\Select::make('staff_id')
                            ->label('Lead Staff')
                            ->relationship('staff', 'full_name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('assignedStaff')
                            ->label('Assigned Staff')
                            ->relationship('assignedStaff', 'full_name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Select all staff involved'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Resources')
                    ->schema([
                        Forms\Components\Select::make('equipment')
                            ->label('Equipment Used')
                            ->relationship('equipment', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Repeater::make('inputs')
                            ->label('Inputs Used')
                            ->relationship('inputs')
                            ->schema([
                                Forms\Components\Select::make('input_type')
                                    ->options([
                                        'fertilizer' => 'Fertilizer',
                                        'pesticide' => 'Pesticide',
                                        'herbicide' => 'Herbicide',
                                        'fungicide' => 'Fungicide',
                                        'seed' => 'Seed',
                                        'water' => 'Water',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('unit')
                                    ->helperText('e.g., kg, liters, bags'),
                                Forms\Components\TextInput::make('cost')
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\DatePicker::make('application_date')
                                    ->label('Applied On'),
                                Forms\Components\TextInput::make('application_method')
                                    ->label('Application Method'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tracking')
                    ->schema([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Total Quantity Used')
                            ->numeric()
                            ->suffix('units')
                            ->helperText('Overall quantity for this activity'),
                        Forms\Components\TextInput::make('cost')
                            ->numeric()
                            ->prefix('$')
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->helperText('Total cost of the activity'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Documentation')
                    ->schema([
                        Forms\Components\Repeater::make('images')
                            ->label('Photos')
                            ->relationship('images')
                            ->schema([
                                Forms\Components\FileUpload::make('image_path')
                                    ->label('Image')
                                    ->image()
                                    ->required()
                                    ->directory('activity-photos')
                                    ->imageEditor(),
                                Forms\Components\TextInput::make('caption')
                                    ->maxLength(255),
                            ])
                            ->columns(1)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Approval')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Select::make('supervisor_id')
                            ->label('Supervisor')
                            ->relationship('supervisor', 'full_name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($record) => $record && $record->exists),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('field.name')
                    ->label('Field')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cropStage.stage_name')
                    ->label('Stage')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('activity_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'weeding' => 'success',
                        'fertilizer_application' => 'warning',
                        'spraying' => 'danger',
                        'irrigation' => 'info',
                        'pruning_training' => 'primary',
                        'scouting_inspection' => 'gray',
                        'thinning_gapping' => 'warning',
                        'soil_crop_nutrition' => 'success',
                        'harvesting' => 'success',
                        default => 'neutral',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('activity_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('staff.full_name')
                    ->label('Lead Staff')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supervisor.full_name')
                    ->label('Supervisor')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('activity_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'neutral',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('activity_type')
                    ->options(Activity::getActivityTypes()),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\Filter::make('activity_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn ($query) => $query->whereDate('activity_date', '>=', $data['from'])
                            )
                            ->when(
                                $data['until'],
                                fn ($query) => $query->whereDate('activity_date', '<=', $data['until'])
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'view' => Pages\ViewActivity::route('/{record}'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
