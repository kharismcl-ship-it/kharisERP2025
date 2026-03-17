<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitorLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Visit Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('hostel.name')
                        ->label('Hostel'),

                    TextEntry::make('occupant.full_name')
                        ->label('Occupant Visited')
                        ->placeholder('—'),

                    TextEntry::make('visitor_name')
                        ->label('Visitor Name'),

                    TextEntry::make('visitor_phone')
                        ->label('Phone')
                        ->placeholder('—'),

                    TextEntry::make('check_in_at')
                        ->label('Checked In')
                        ->dateTime(),

                    TextEntry::make('check_out_at')
                        ->label('Checked Out')
                        ->dateTime()
                        ->placeholder('Still on premises'),

                    TextEntry::make('purpose')
                        ->label('Purpose')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}
