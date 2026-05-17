<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedTypeResource\Pages;
use App\Models\FeedType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeedTypeResource extends Resource
{
    protected static ?string $model = FeedType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Farm Operations';

    public static function canAccess(): bool
    {
        // Allow admin and manager roles to access this resource in Filament
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        $role = $user->role;

        // Assuming role is a string like 'admin', 'manager', or 'user'
        // Adjust this if your role storage is different (e.g., JSON, multiple roles)
        return in_array($role, ['admin', 'manager']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535),
                Forms\Components\Select::make('default_unit')
                    ->options([
                        'kg' => 'Kilograms',
                        'g' => 'Grams',
                        'lbs' => 'Pounds',
                        'bags' => 'Bags',
                        'tons' => 'Tons',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('min_threshold')
                    ->label('Minimum Threshold')
                    ->type('number')
                    ->step('0.01')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_unit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('min_threshold')
                    ->label('Min Threshold')
                    ->numeric()
                    ->sortable(),
                 Tables\Columns\ToggleColumn::make('is_active')
                     ->label('Active'),
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
            'index' => Pages\ListFeedTypes::route('/'),
            'create' => Pages\CreateFeedType::route('/create'),
            'edit' => Pages\EditFeedType::route('/{record}/edit'),
        ];
    }
}