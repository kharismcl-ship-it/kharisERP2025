<?php

namespace Modules\Hostels\Filament\Resources;

use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\HostelMoviePurchaseResource\Pages;
use Modules\Hostels\Models\HostelMoviePurchase;

class HostelMoviePurchaseResource extends Resource
{
    protected static ?string $model = HostelMoviePurchase::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('hostel_movie_id')
                    ->relationship('movie', 'title'),
                Forms\Components\Select::make('hostel_occupant_id')
                    ->relationship('occupant', 'full_name'),
                Forms\Components\TextInput::make('amount_paid')
                    ->numeric()
                    ->prefix('GHS'),
                Forms\Components\DateTimePicker::make('paid_at'),
                Forms\Components\DateTimePicker::make('expires_at'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                        'expired' => 'Expired',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('movie.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('occupant.full_name')
                    ->label('Occupant')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'    => 'success',
                        'expired' => 'danger',
                        default   => 'warning',
                    }),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                        'expired' => 'Expired',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListHostelMoviePurchases::route('/'),
        ];
    }
}
