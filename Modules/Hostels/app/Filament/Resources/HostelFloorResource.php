<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Filament\Resources\HostelFloorResource\Pages;
use Modules\Hostels\Models\HostelFloor;

class HostelFloorResource extends Resource
{
    protected static ?string $model = HostelFloor::class;

    protected static ?string $slug = 'hostel-floors';

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

                Select::make('hostel_block_id')
                    ->relationship('hostelBlock', 'name')
                    ->searchable()
                    ->nullable(),

                TextInput::make('name')
                    ->required(),

                TextInput::make('level')
                    ->numeric()
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

                TextColumn::make('hostelBlock.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('level'),
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
            'index' => Pages\ListHostelFloors::route('/'),
            'create' => Pages\CreateHostelFloor::route('/create'),
            'edit' => Pages\EditHostelFloor::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['hostel', 'hostelBlock']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'hostel.name', 'hostelBlock.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->hostel) {
            $details['Hostel'] = $record->hostel->name;
        }

        if ($record->hostelBlock) {
            $details['HostelBlock'] = $record->hostelBlock->name;
        }

        return $details;
    }
}
