<?php

namespace App\Filament\Resources;

    use App\Filament\Resources\HostelFloorResource\Pages;
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
    use Modules\Hostels\Models\HostelFloor;

    class HostelFloorResource extends Resource {
        protected static ?string $model = HostelFloor::class;

        protected static ?string $slug = 'hostel-floors';

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

        PUBLIC static function form(Schema $schema): Schema
        {
        return $schema
        ->components([//
        Select::make('hostel_id')
        ->relationship('hostel', 'name')
        ->searchable()
        ->required(),

        Select::make('hostel_block_id')
        ->relationship('hostelBlock', 'name')
        ->searchable()
        ->required(),

        TextInput::make('name')
        ->required(),

        TextInput::make('level')
        ->integer(),

        TextEntry::make('created_at')
        ->label('Created Date')
        ->state(fn (?HostelFloor $record): string => $record?->created_at?->diffForHumans() ?? '-'),

        TextEntry::make('updated_at')
        ->label('Last Modified Date')
        ->state(fn (?HostelFloor $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
        }

        PUBLIC static function table(Table $table): Table
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

        PUBLIC static function getGlobalSearchEloquentQuery(): Builder
        {
        return parent::getGlobalSearchEloquentQuery()->with(['hostel', 'hostelBlock']);
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return ['name', 'hostel.name', 'hostelBlock.name'];
        }

        PUBLIC static function getGlobalSearchResultDetails(Model $record): array
        {
        $details = [];

        if ($record->hostel) {
$details['Hostel'] = $record->hostel->name;}

if ($record->hostelBlock) {
$details['HostelBlock'] = $record->hostelBlock->name;}

        return $details;
        }
    }
