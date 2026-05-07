<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CropStageResource\Pages;
use App\Models\CropStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CropStageResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = CropStage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('crop_cycle_id')
                    ->relationship('cropCycle', 'id')
                    ->required(),
                Forms\Components\TextInput::make('stage_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\DatePicker::make('end_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cropCycle.crop_name')
                    ->label('Crop Cycle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stage_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
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
                //
            ])
            ->actions([
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
            'index' => Pages\ListCropStages::route('/'),
            'create' => Pages\CreateCropStage::route('/create'),
            'edit' => Pages\EditCropStage::route('/{record}/edit'),
        ];
    }
}
