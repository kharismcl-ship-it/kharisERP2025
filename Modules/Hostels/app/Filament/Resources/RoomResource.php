<?php

namespace Modules\Hostels\Filament\Resources;

    use BackedEnum;
    use Filament\Actions\BulkActionGroup;
    use Filament\Actions\DeleteAction;
    use Filament\Actions\DeleteBulkAction;
    use Filament\Actions\EditAction;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\TextInput;
    use Filament\Infolists\Components\TextEntry;
    use Filament\Resources\Resource;
    use Filament\Schemas\Schema;
    use Filament\Support\Icons\Heroicon;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Modules\Hostels\Filament\Resources\RoomResource\Pages;
    use Modules\Hostels\Filament\Resources\RoomResource\RelationManagers\BedsRelationManager;
    use Modules\Hostels\Models\Room;

    class RoomResource extends Resource {
        protected static ?string $model = Room::class;

        protected static ?string $slug = 'rooms';

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

        PUBLIC static function form(Schema $schema): Schema
        {
        return $schema
        ->components([//
        Select::make('hostel_id')
        ->relationship('hostel', 'name')
        ->searchable()
        ->required(),

        TextInput::make('room_number')
        ->required(),

        TextInput::make('description'),

        TextEntry::make('created_at')
        ->label('Created Date')
        ->state(fn (?Room $record): string => $record?->created_at?->diffForHumans() ?? '-'),

        TextEntry::make('updated_at')
        ->label('Last Modified Date')
        ->state(fn (?Room $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
        }

        PUBLIC static function table(Table $table): Table
        {
        return $table
        ->columns([
        TextColumn::make('hostel.name')
        ->searchable()
        ->sortable(),

        TextColumn::make('room_number'),

        TextColumn::make('description'),
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

        public static function getRelations(): array
        {
        return [
        BedsRelationManager::class,
        ];
        }

        public static function getPages(): array
        {
        return [
        'index' => Pages\ListRooms::route('/'),
'create' => Pages\CreateRoom::route('/create'),
'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
        }

        PUBLIC static function getGlobalSearchEloquentQuery(): Builder
        {
        return parent::getGlobalSearchEloquentQuery()->with(['hostel']);
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return ['hostel.name'];
        }

        PUBLIC static function getGlobalSearchResultDetails(Model $record): array
        {
        $details = [];

        if ($record->hostel) {
$details['Hostel'] = $record->hostel->name;}

        return $details;
        }
    }
