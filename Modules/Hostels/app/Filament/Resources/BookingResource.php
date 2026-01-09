<?php

namespace Modules\Hostels\Filament\Resources;

    use BackedEnum;
    use Filament\Actions\BulkActionGroup;
    use Filament\Actions\DeleteAction;
    use Filament\Actions\DeleteBulkAction;
    use Filament\Actions\EditAction;
    use Filament\Forms\Components\DatePicker;
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
    use Modules\Hostels\Filament\Resources\BookingResource\Pages;
    use Modules\Hostels\Models\Booking;

    class BookingResource extends Resource {
        protected static ?string $model = Booking::class;

        protected static ?string $slug = 'bookings';

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

        PUBLIC static function form(Schema $schema): Schema
        {
        return $schema
        ->components([//
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

        PUBLIC static function table(Table $table): Table
        {
        return $table
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
        'index' => Pages\ListBookings::route('/'),
'create' => Pages\CreateBooking::route('/create'),
'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
        }

        PUBLIC static function getGlobalSearchEloquentQuery(): Builder
        {
        return parent::getGlobalSearchEloquentQuery()->with(['tenant']);
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return ['tenant.name'];
        }

        PUBLIC static function getGlobalSearchResultDetails(Model $record): array
        {
        $details = [];

        if ($record->tenant) {
$details['Tenant'] = $record->tenant->name;}

        return $details;
        }
    }
