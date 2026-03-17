<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Schemas;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;

class VisitorLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Visitor Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('hostel_id')
                        ->label('Hostel')
                        ->options(fn () => Hostel::where('company_id', Filament::getTenant()?->id)
                            ->pluck('name', 'id'))
                        ->required()
                        ->native(false)
                        ->live(),

                    Forms\Components\Select::make('hostel_occupant_id')
                        ->label('Visiting Occupant')
                        ->options(fn (Get $get) => $get('hostel_id')
                            ? HostelOccupant::where('hostel_id', $get('hostel_id'))
                                ->get()
                                ->pluck('full_name', 'id')
                            : [])
                        ->searchable()
                        ->nullable()
                        ->native(false)
                        ->placeholder('Not visiting a specific occupant'),

                    Forms\Components\TextInput::make('visitor_name')
                        ->label('Visitor Name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('visitor_phone')
                        ->label('Visitor Phone')
                        ->tel()
                        ->nullable(),

                    Forms\Components\DateTimePicker::make('check_in_at')
                        ->label('Check-In Time')
                        ->required()
                        ->native(false)
                        ->default(now()),

                    Forms\Components\DateTimePicker::make('check_out_at')
                        ->label('Check-Out Time')
                        ->native(false)
                        ->nullable()
                        ->after('check_in_at'),

                    Forms\Components\Textarea::make('purpose')
                        ->label('Purpose of Visit')
                        ->rows(2)
                        ->nullable()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
