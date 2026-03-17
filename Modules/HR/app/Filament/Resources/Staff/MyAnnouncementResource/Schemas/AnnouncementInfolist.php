<?php

namespace Modules\HR\Filament\Resources\Staff\MyAnnouncementResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnnouncementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Announcement')
                ->columns(2)
                ->schema([
                    TextEntry::make('title')
                        ->columnSpanFull(),
                    TextEntry::make('priority')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'urgent' => 'danger',
                            'high'   => 'warning',
                            'normal' => 'info',
                            'low'    => 'gray',
                            default  => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst($state ?? '')),
                    TextEntry::make('published_at')->dateTime(),
                    TextEntry::make('expires_at')
                        ->dateTime()
                        ->placeholder('No expiry'),
                    TextEntry::make('content')
                        ->columnSpanFull()
                        ->placeholder('—')
                        ->html(),
                ]),
        ]);
    }
}
