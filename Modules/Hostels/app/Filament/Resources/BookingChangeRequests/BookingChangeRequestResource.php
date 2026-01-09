<?php

namespace Modules\Hostels\Filament\Resources\BookingChangeRequests;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\Pages\CreateBookingChangeRequest;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\Pages\EditBookingChangeRequest;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\Pages\ListBookingChangeRequests;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\Pages\ViewBookingChangeRequest;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\Schemas\BookingChangeRequestForm;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\Schemas\BookingChangeRequestInfolist;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\Tables\BookingChangeRequestsTable;
use Modules\Hostels\Models\BookingChangeRequest;

class BookingChangeRequestResource extends Resource
{
    protected static ?string $model = BookingChangeRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return BookingChangeRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookingChangeRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingChangeRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookingChangeRequests::route('/'),
            'create' => CreateBookingChangeRequest::route('/create'),
            'view' => ViewBookingChangeRequest::route('/{record}'),
            'edit' => EditBookingChangeRequest::route('/{record}/edit'),
        ];
    }
}
