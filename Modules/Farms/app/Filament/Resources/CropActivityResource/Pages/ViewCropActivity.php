<?php

namespace Modules\Farms\Filament\Resources\CropActivityResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\CropActivityResource;

class ViewCropActivity extends ViewRecord
{
    protected static string $resource = CropActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make(), DeleteAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Activity Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('activity_type')
                        ->badge()
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                        ->color('primary'),
                    TextEntry::make('activity_date')->date('d M Y'),
                    TextEntry::make('farm.name')->label('Farm'),
                    TextEntry::make('cropCycle.crop_name')->label('Crop Cycle'),
                    TextEntry::make('scouted_by')->label('Performed By')->placeholder('—'),
                ]),

            Section::make('Labour & Cost')
                ->columns(3)
                ->schema([
                    TextEntry::make('duration_hours')->label('Duration (hrs)')->placeholder('—'),
                    TextEntry::make('labour_count')->label('Workers'),
                    TextEntry::make('cost')->money('GHS')->label('Cost')->placeholder('—'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Audit')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema([
                    TextEntry::make('created_at')->dateTime('d M Y H:i'),
                    TextEntry::make('updated_at')->dateTime('d M Y H:i'),
                ]),
        ]);
    }
}