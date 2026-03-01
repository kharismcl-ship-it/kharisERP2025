<?php

namespace Modules\HR\Filament\Resources\KpiDefinitionResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\KpiDefinitionResource;
use Modules\HR\Models\KpiDefinition;

class ViewKpiDefinition extends ViewRecord
{
    protected static string $resource = KpiDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('KPI Details')->columns(2)->schema([
                TextEntry::make('name')->weight('bold'),
                TextEntry::make('company.name')->label('Company'),
                TextEntry::make('department.name')->label('Department')->placeholder('All Departments'),
                TextEntry::make('jobPosition.title')->label('Job Position')->placeholder('All Positions'),
                TextEntry::make('unit_of_measure')->label('Unit of Measure')->placeholder('—'),
                TextEntry::make('target_value')->label('Target Value')->placeholder('—'),
                TextEntry::make('frequency')->label('Frequency')
                    ->formatStateUsing(fn ($state) => KpiDefinition::FREQUENCIES[$state] ?? ucfirst($state ?? '')),
                IconEntry::make('is_active')->label('Active')->boolean(),
                TextEntry::make('description')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}