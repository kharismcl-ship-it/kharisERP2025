<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource\Schemas;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\Room;

class MaintenanceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maintenance Request')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('hostel_id')
                        ->label('Hostel')
                        ->options(fn () => Hostel::where('company_id', Filament::getTenant()?->id)
                            ->pluck('name', 'id'))
                        ->required()
                        ->native(false)
                        ->live(),

                    Forms\Components\Select::make('priority')
                        ->options([
                            'low'    => 'Low',
                            'medium' => 'Medium',
                            'high'   => 'High',
                            'urgent' => 'Urgent',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('room_id')
                        ->label('Room (optional)')
                        ->options(fn (Get $get) => $get('hostel_id')
                            ? Room::where('hostel_id', $get('hostel_id'))->pluck('room_number', 'id')
                            : [])
                        ->searchable()
                        ->nullable()
                        ->native(false),

                    Forms\Components\Select::make('bed_id')
                        ->label('Bed (optional)')
                        ->relationship('bed', 'bed_number')
                        ->searchable()
                        ->nullable()
                        ->native(false),

                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
