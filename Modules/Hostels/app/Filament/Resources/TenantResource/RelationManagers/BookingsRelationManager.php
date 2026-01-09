<?php

namespace Modules\Hostels\Filament\Resources\TenantResource\RelationManagers;

    use Filament\Actions\BulkActionGroup;
    use Filament\Actions\CreateAction;
    use Filament\Actions\DeleteAction;
    use Filament\Actions\DeleteBulkAction;
    use Filament\Actions\EditAction;
    use Filament\Forms\Components\DatePicker;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\TextInput;
    use Filament\Infolists\Components\TextEntry;
    use Filament\Resources\RelationManagers\RelationManager;
    use Filament\Schemas\Schema;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;
    use Modules\Hostels\Models\Booking;

    class BookingsRelationManager extends RelationManager {
        protected static string $relationship = 'bookings';

        PUBLIC function form(Schema $schema): Schema
        {
        return $schema
        ->components([
        Select::make('tenant_id')
        ->relationship('tenant', 'name')
        ->searchable()
        ->required(),

        TextInput::make('bed_id')
        ->required()
        ->integer(),

        DatePicker::make('start_date'),

        DatePicker::make('end_date'),

        TextInput::make('status')
        ->required(),

        TextEntry::make('created_at')
        ->label('Created Date')
        ->state(fn (?Booking $record): string => $record?->created_at?->diffForHumans() ?? '-'),

        TextEntry::make('updated_at')
        ->label('Last Modified Date')
        ->state(fn (?Booking $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
        }

        PUBLIC function table(Table $table): Table
        {
        return $table
        ->recordTitleAttribute('id')
        ->columns([
        TextColumn::make('tenant.name')
        ->searchable()
        ->sortable(),

        TextColumn::make('bed_id'),

        TextColumn::make('start_date')
        ->date(),

        TextColumn::make('end_date')
        ->date(),

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
