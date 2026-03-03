<?php

namespace Modules\Hostels\Filament\Resources\RoomResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Hostels\Filament\Resources\RoomResource;

class ViewRoom extends ViewRecord
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Basic Information')
                ->schema([
                    TextEntry::make('hostel.name')->label('Hostel'),
                    TextEntry::make('room_number')->label('Room Number'),
                    TextEntry::make('block.name')->label('Block')->placeholder('—'),
                    TextEntry::make('floor.name')->label('Floor')->placeholder('—'),
                ])
                ->columns(2),

            Section::make('Configuration')
                ->schema([
                    TextEntry::make('type')->label('Room Type')->badge(),
                    TextEntry::make('gender_policy')->label('Gender Policy')->badge(),
                    TextEntry::make('billing_cycle')->label('Billing Cycle')->badge(),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'available'           => 'success',
                            'partially_occupied'  => 'info',
                            'full'                => 'warning',
                            'maintenance'         => 'danger',
                            'closed'              => 'gray',
                            default               => 'gray',
                        }),
                ])
                ->columns(2),

            Section::make('Occupancy')
                ->schema([
                    TextEntry::make('max_occupancy')->label('Max Occupancy'),
                    TextEntry::make('current_occupancy')->label('Current Occupancy'),
                ])
                ->columns(2),

            Section::make('Pricing')
                ->schema([
                    TextEntry::make('base_rate')->label('Base Rate')->money('GHS'),
                    TextEntry::make('per_night_rate')->label('Per Night Rate')->money('GHS')->placeholder('—'),
                    TextEntry::make('per_semester_rate')->label('Per Semester Rate')->money('GHS')->placeholder('—'),
                    TextEntry::make('per_year_rate')->label('Per Year Rate')->money('GHS')->placeholder('—'),
                ])
                ->columns(2),

            Section::make('Notes')
                ->schema([
                    TextEntry::make('notes')->columnSpanFull()->placeholder('No notes.'),
                ])
                ->collapsed(),
        ]);
    }
}
