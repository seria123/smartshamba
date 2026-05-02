<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InputResource\Pages;
use App\Filament\Resources\InputResource\RelationManagers;
use App\Models\Input;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InputResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Input::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('activity_id')
                    ->label('Activity')
                    ->relationship('activity', 'activity_name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('input_type')
                    ->options([
                        'fertilizer' => 'Fertilizer',
                        'seed' => 'Seed',
                        'chemical' => 'Chemical',
                        'pesticide' => 'Pesticide',
                        'water' => 'Water',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('activity.activity_name')
                    ->label('Activity')
                    ->searchable(),
                Tables\Columns\TextColumn::make('input_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
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
            'index' => Pages\ListInputs::route('/'),
            'create' => Pages\CreateInput::route('/create'),
            'edit' => Pages\EditInput::route('/{record}/edit'),
        ];
    }
}
