<?php

namespace App\Filament\Resources;

use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Resources\BookingResource\Pages\CreateBooking;
use App\Filament\Resources\BookingResource\Pages\EditBooking;
use App\Filament\Resources\BookingResource\Pages\ListBookings;
use Filament\Tables;
use Filament\Tables\Table;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Operations';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Client')
                ->relationship('user', 'name')
                ->native(false)
                ->searchable(),
            Forms\Components\Select::make('provider_id')
                ->label('Provider')
                ->relationship('provider', 'company_name')
                ->native(false)
                ->searchable(),
            Forms\Components\TextInput::make('service_name')->required()->maxLength(255),
            Forms\Components\DatePicker::make('scheduled_date')->required(),
            Forms\Components\TimePicker::make('scheduled_time')->required()->seconds(false),
            Forms\Components\TextInput::make('address')->maxLength(255),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'ongoing' => 'Ongoing',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])->required(),
            Forms\Components\TextInput::make('price')->numeric()->step('0.01'),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
            Forms\Components\TextInput::make('rating')->numeric()->minValue(0)->maxValue(5)->step('0.1'),
            Forms\Components\DateTimePicker::make('rated_at'),
            Forms\Components\DateTimePicker::make('completed_at'),
            Forms\Components\DateTimePicker::make('cancelled_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('provider.company_name')->label('Provider')->searchable(),
                Tables\Columns\TextColumn::make('service_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('scheduled_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('scheduled_time')->dateTime('H:i')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('price')->money('PHP', true),
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
            'index' => ListBookings::route('/'),
            'create' => CreateBooking::route('/create'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }
}
