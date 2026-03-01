<?php

namespace Modules\HR\Filament\Resources\JobPositionResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\JobPositionResource;

class ViewJobPosition extends ViewRecord
{
    protected static string $resource = JobPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Position Details')->columns(2)->schema([
                TextEntry::make('title')->label('Job Title')->weight('bold'),
                TextEntry::make('code')->placeholder('—'),
                TextEntry::make('company.name')->label('Company'),
                TextEntry::make('department.name')->label('Department')->placeholder('—'),
                TextEntry::make('grade')->placeholder('—'),
                IconEntry::make('is_active')->label('Active')->boolean(),
                TextEntry::make('description')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}
