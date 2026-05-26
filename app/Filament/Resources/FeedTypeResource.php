<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedTypeResource\Pages;
use App\Models\FeedType;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class FeedTypeResource extends Resource
{
    protected static ?string $model = FeedType::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-archive';

    protected static ?string $navigationLabel = 'Feed Types';

    protected static ?string $modelLabel = 'Feed Type';

    protected static ?string $pluralModelLabel = 'Feed Types';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('description')
                    ->maxLength(255),
                TextInput::make('default_unit')
                    ->required()
                    ->maxLength(50),
                TextInput::make('min_threshold')
                    ->required()
                    ->numeric()
                    ->prefix('kg '),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('default_unit')
                    ->searchable(),
                TextColumn::make('min_threshold')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedTypes::route('/'),
            'create' => Pages\CreateFeedType::route('/create'),
            'view' => Pages\ViewFeedType::route('/{record}'),
            'edit' => Pages\EditFeedType::route('/{record}/edit'),
        ];
    }
}