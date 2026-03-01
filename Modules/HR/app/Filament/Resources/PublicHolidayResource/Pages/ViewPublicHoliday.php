<?php

namespace Modules\HR\Filament\Resources\PublicHolidayResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\PublicHolidayResource;

class ViewPublicHoliday extends ViewRecord
{
    protected static string $resource = PublicHolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Holiday Details')->columns(2)->schema([
                TextEntry::make('name')->weight('bold'),
                TextEntry::make('date')->date(),
                TextEntry::make('company.name')->label('Company'),
                IconEntry::make('is_recurring_annually')->label('Recurring Annually')->boolean(),
                TextEntry::make('description')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}