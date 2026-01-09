<?php

namespace App\Filament\Resources;

    use App\Filament\Resources\HostelBlockResource\Pages;
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
    use Modules\Hostels\Models\HostelBlock;

    class HostelBlockResource extends Resource {
        protected static ?string $model = HostelBlock::class;

        protected static ?string $slug = 'hostel-blocks';

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

        PUBLIC static function form(Schema $schema): Schema
        {
        return $schema
        ->components([//
        Select::make('hostel_id')
        ->relationship('hostel', 'name')
        ->searchable()
        ->required(),

        TextInput::make('name')
        ->required(),

        TextInput::make('gender_option'),

        TextInput::make('description')
        ->required(),

        TextEntry::make('created_at')
        ->label('Created Date')
        ->state(fn (?HostelBlock $record): string => $record?->created_at?->diffForHumans() ?? '-'),

        TextEntry::make('updated_at')
        ->label('Last Modified Date')
        ->state(fn (?HostelBlock $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
        }

        PUBLIC static function table(Table $table): Table
        {
        return $table
        ->columns([
        TextColumn::make('hostel.name')
        ->searchable()
        ->sortable(),

        TextColumn::make('name')
        ->searchable()
        ->sortable(),

        TextColumn::make('gender_option'),

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

        public static function getPages(): array
        {
        return [
        'index' => Pages\ListHostelBlocks::route('/'),
'create' => Pages\CreateHostelBlock::route('/create'),
'edit' => Pages\EditHostelBlock::route('/{record}/edit'),
        ];
        }

        PUBLIC static function getGlobalSearchEloquentQuery(): Builder
        {
        return parent::getGlobalSearchEloquentQuery()->with(['hostel']);
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return ['name', 'hostel.name'];
        }

        PUBLIC static function getGlobalSearchResultDetails(Model $record): array
        {
        $details = [];

        if ($record->hostel) {
$details['Hostel'] = $record->hostel->name;}

        return $details;
        }
    }
