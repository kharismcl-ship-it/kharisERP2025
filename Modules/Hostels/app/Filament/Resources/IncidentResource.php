<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\IncidentResource\Pages;
use Modules\Hostels\Models\Incident;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    /**
     * This model has no direct company_id — Filament's ownership
     * check is skipped. Data isolation is handled via the parent
     * relationship or a custom getEloquentQuery() scope.
     */
    protected static bool $isScopedToTenant = false;

    protected static ?string $slug = 'incidents';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 9;

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

                Select::make('room_id')
                    ->relationship('room', 'room_number')
                    ->searchable()
                    ->nullable(),

                TextInput::make('title')
                    ->required(),

                Textarea::make('description')
                    ->required(),

                Select::make('severity')
                    ->options([
                        'minor' => 'Minor',
                        'major' => 'Major',
                        'critical' => 'Critical',
                    ])
                    ->required(),

                Select::make('reported_by_user_id')
                    ->relationship('reportedByUser', 'name')
                    ->searchable()
                    ->required(),

                Textarea::make('action_taken')
                    ->nullable(),

                Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'resolved' => 'Resolved',
                        'escalated' => 'Escalated',
                    ])
                    ->required()
                    ->default('open'),

                DateTimePicker::make('reported_at')
                    ->required(),

                DateTimePicker::make('resolved_at')
                    ->nullable(),
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

                TextColumn::make('room.room_number')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('severity')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reportedByUser.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reported_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('resolved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),
            'view' => Pages\ViewIncident::route('/{record}'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
