<?php

namespace Modules\Construction\Filament\Resources\MaterialUsageResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\MaterialUsageResource;

class ViewMaterialUsage extends ViewRecord
{
    protected static string $resource = MaterialUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Material Details')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('project.name')->label('Project'),
                    TextEntry::make('phase.name')->label('Phase')->placeholder('—'),
                    TextEntry::make('usage_date')->label('Usage Date')->date(),
                ]),
                Grid::make(3)->schema([
                    TextEntry::make('material_name')->label('Material'),
                    TextEntry::make('unit'),
                    TextEntry::make('supplier')->placeholder('—'),
                ]),
            ]),

            Section::make('Quantities & Cost')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('quantity'),
                    TextEntry::make('unit_cost')->label('Unit Cost')->money('GHS'),
                    TextEntry::make('total_cost')->label('Total Cost')->money('GHS'),
                ]),
            ]),

            Section::make('Notes')->schema([
                TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}
