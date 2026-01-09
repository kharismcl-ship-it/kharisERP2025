<?php

namespace Modules\Hostels\Filament\Resources\RoomResource\RelationManagers;

    use Filament\Actions\BulkActionGroup;
    use Filament\Actions\CreateAction;
    use Filament\Actions\DeleteAction;
    use Filament\Actions\DeleteBulkAction;
    use Filament\Actions\EditAction;
    use Filament\Forms\Components\TextInput;
    use Filament\Infolists\Components\TextEntry;
    use Filament\Resources\RelationManagers\RelationManager;
    use Filament\Schemas\Schema;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;
    use Modules\Hostels\Models\Bed;

    class BedsRelationManager extends RelationManager {
        protected static string $relationship = 'beds';

        PUBLIC function form(Schema $schema): Schema
        {
        return $schema
        ->components([
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

        PUBLIC function table(Table $table): Table
        {
        return $table
        ->recordTitleAttribute('id')
        ->columns([
        TextColumn::make('room_id'),

        TextColumn::make('bed_number'),

        TextColumn::make('status'),
        ])
        ->filters([
        //
        ])
        ->headerActions([
        CreateAction::make(),
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
    }
