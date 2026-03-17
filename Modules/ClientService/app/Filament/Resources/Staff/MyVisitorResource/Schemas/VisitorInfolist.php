<?php

namespace Modules\ClientService\Filament\Resources\Staff\MyVisitorResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Visitor Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('full_name')->label('Name')->weight('bold'),
                    TextEntry::make('organization')->placeholder('—'),
                    TextEntry::make('phone')->placeholder('—'),
                    TextEntry::make('email')->placeholder('—'),
                    TextEntry::make('purpose_of_visit')->label('Purpose')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Visit Record')
                ->columns(3)
                ->schema([
                    TextEntry::make('check_in_at')->dateTime()->label('Checked In'),
                    TextEntry::make('check_out_at')->dateTime()->label('Checked Out')->placeholder('Still on premises'),
                    TextEntry::make('duration')->placeholder('—'),
                    IconEntry::make('is_checked_out')->label('Has Left')->boolean(),
                    TextEntry::make('badge_number')->label('Badge #')->placeholder('—'),
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }
}
