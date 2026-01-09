<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\VisitorLogResource\Pages;
use Modules\Hostels\Models\VisitorLog;

class VisitorLogResource extends Resource
{
    protected static ?string $model = VisitorLog::class;

    protected static ?string $slug = 'visitor-logs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->searchable()
                    ->required(),

                Select::make('hostel_occupant_id')
                    ->relationship('occupant', 'first_name')
                    ->searchable()
                    ->nullable(),

                TextInput::make('visitor_name')
                    ->required(),

                TextInput::make('visitor_phone')
                    ->tel()
                    ->nullable(),

                Textarea::make('purpose')
                    ->nullable(),

                DateTimePicker::make('check_in_at')
                    ->required(),

                DateTimePicker::make('check_out_at')
                    ->nullable(),

                Select::make('recorded_by_user_id')
                    ->relationship('recordedByUser', 'name')
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('occupant.first_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('visitor_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('visitor_phone')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('purpose')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('check_in_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('check_out_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('recordedByUser.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisitorLogs::route('/'),
            'create' => Pages\CreateVisitorLog::route('/create'),
            'edit' => Pages\EditVisitorLog::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
