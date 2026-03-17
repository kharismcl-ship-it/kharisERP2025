<?php

namespace Modules\HR\Filament\Resources\Staff\MyCertificationResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Certification Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')
                        ->label('Certification Name')
                        ->columnSpanFull(),
                    TextEntry::make('issuing_authority'),
                    TextEntry::make('certificate_number')
                        ->placeholder('—'),
                    TextEntry::make('issue_date')->date(),
                    TextEntry::make('expiry_date')
                        ->date()
                        ->placeholder('No Expiry')
                        ->color(fn ($record) => $record?->is_expired ? 'danger' : ($record?->is_expiring_soon ? 'warning' : 'success')),
                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}
