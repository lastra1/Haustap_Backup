<?php

namespace App\Filament\Resources;

use App\Models\ServiceProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Resources\ServiceProviderResource\Pages\CreateServiceProvider;
use App\Filament\Resources\ServiceProviderResource\Pages\EditServiceProvider;
use App\Filament\Resources\ServiceProviderResource\Pages\ListServiceProviders;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceProviderResource extends Resource
{
    protected static ?string $model = ServiceProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $navigationLabel = 'Providers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->native(false)
                ->searchable(),
            Forms\Components\TextInput::make('company_name')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->columnSpanFull(),
            Forms\Components\TextInput::make('location')->maxLength(255),
            Forms\Components\TextInput::make('rating')->numeric()->minValue(0)->maxValue(5)->step('0.1'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('company_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('location')->searchable(),
                Tables\Columns\TextColumn::make('rating'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceProviders::route('/'),
            'create' => CreateServiceProvider::route('/create'),
            'edit' => EditServiceProvider::route('/{record}/edit'),
        ];
    }
}
