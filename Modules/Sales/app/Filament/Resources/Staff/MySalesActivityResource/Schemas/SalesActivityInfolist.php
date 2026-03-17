<?php

namespace Modules\Sales\Filament\Resources\Staff\MySalesActivityResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalesActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Activity')
                ->columns(3)
                ->schema([
                    TextEntry::make('subject')->columnSpanFull()->weight('bold'),
                    TextEntry::make('type')
                        ->badge()
                        ->formatStateUsing(fn ($state) => ucfirst($state)),
                    TextEntry::make('scheduled_at')->dateTime()->label('Scheduled')->placeholder('—'),
                    TextEntry::make('completed_at')->dateTime()->label('Completed')->placeholder('—'),
                    TextEntry::make('outcome')->columnSpanFull()->placeholder('—'),
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }
}
