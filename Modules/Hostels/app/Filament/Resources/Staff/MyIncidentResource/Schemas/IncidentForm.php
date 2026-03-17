<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Schemas;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Room;

class IncidentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Incident Report')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('hostel_id')
                        ->label('Hostel')
                        ->options(fn () => Hostel::where('company_id', Filament::getTenant()?->id)
                            ->pluck('name', 'id'))
                        ->required()
                        ->native(false)
                        ->live(),

                    Forms\Components\Select::make('severity')
                        ->options([
                            'minor'    => 'Minor',
                            'major'    => 'Major',
                            'critical' => 'Critical',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('room_id')
                        ->label('Room (optional)')
                        ->options(fn (Get $get) => $get('hostel_id')
                            ? Room::where('hostel_id', $get('hostel_id'))->pluck('room_number', 'id')
                            : [])
                        ->searchable()
                        ->nullable()
                        ->native(false),

                    Forms\Components\Select::make('hostel_occupant_id')
                        ->label('Occupant Involved (optional)')
                        ->options(fn (Get $get) => $get('hostel_id')
                            ? HostelOccupant::where('hostel_id', $get('hostel_id'))
                                ->get()
                                ->pluck('full_name', 'id')
                            : [])
                        ->searchable()
                        ->nullable()
                        ->native(false),

                    Forms\Components\Textarea::make('action_taken')
                        ->label('Immediate Action Taken')
                        ->rows(2)
                        ->nullable()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
