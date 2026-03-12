<?php

namespace Modules\Hostels\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\HostelBookOrderResource\Pages;
use Modules\Hostels\Models\HostelBookOrder;

class HostelBookOrderResource extends Resource
{
    protected static ?string $model = HostelBookOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 24;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('hostel_occupant_id')
                    ->relationship('occupant', 'full_name')
                    ->required(),
                Forms\Components\Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->required(),
                Forms\Components\TextInput::make('reference')
                    ->disabled(),
                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->prefix('GHS'),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->prefix('GHS'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'paid'       => 'Paid',
                        'processing' => 'Processing',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('paid_at'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hostel.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('occupant.full_name')
                    ->label('Occupant')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'warning',
                        'paid'       => 'success',
                        'processing' => 'info',
                        'delivered'  => 'primary',
                        'cancelled'  => 'gray',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('paid_at')
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
                        'pending'    => 'Pending',
                        'paid'       => 'Paid',
                        'processing' => 'Processing',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index'  => Pages\ListHostelBookOrders::route('/'),
            'create' => Pages\CreateHostelBookOrder::route('/create'),
            'edit'   => Pages\EditHostelBookOrder::route('/{record}/edit'),
        ];
    }
}
