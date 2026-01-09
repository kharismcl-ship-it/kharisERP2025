<?php

namespace Modules\Hostels\Filament\Resources;

    use BackedEnum;
    use Filament\Actions\BulkActionGroup;
    use Filament\Actions\DeleteAction;
    use Filament\Actions\DeleteBulkAction;
    use Filament\Actions\EditAction;
    use Filament\Forms\Components\TextInput;
    use Filament\Infolists\Components\TextEntry;
    use Filament\Resources\Resource;
    use Filament\Schemas\Schema;
    use Filament\Support\Icons\Heroicon;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;
    use Modules\Hostels\Filament\Resources\BedResource\Pages;
    use Modules\Hostels\Filament\Resources\BedResource\RelationManagers\BookingsRelationManager;
    use Modules\Hostels\Models\Bed;

    class BedResource extends Resource {
        protected static ?string $model = Bed::class;

        protected static ?string $slug = 'beds';

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

        PUBLIC static function form(Schema $schema): Schema
        {
        return $schema
        ->components([//
        TextInput::make('room_id')
        ->required()
        ->integer(),

        TextInput::make('bed_number')
        ->required(),

        TextInput::make('status')
        ->required(),

        TextEntry::make('created_at')
        ->label('Created Date')
        ->state(fn (?Bed $record): string => $record?->created_at?->diffForHumans() ?? '-'),

        TextEntry::make('updated_at')
        ->label('Last Modified Date')
        ->state(fn (?Bed $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
        }

        PUBLIC static function table(Table $table): Table
        {
        return $table
        ->columns([
        TextColumn::make('room_id'),

        TextColumn::make('bed_number'),

        TextColumn::make('status'),
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
        BookingsRelationManager::class,
        ];
        }

        public static function getPages(): array
        {
        return [
        'index' => Pages\ListBeds::route('/'),
'create' => Pages\CreateBed::route('/create'),
'edit' => Pages\EditBed::route('/{record}/edit'),
        ];
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return [];
        }
    }
