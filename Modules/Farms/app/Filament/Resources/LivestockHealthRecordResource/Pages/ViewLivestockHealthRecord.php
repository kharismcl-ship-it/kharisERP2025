<?php

namespace Modules\Farms\Filament\Resources\LivestockHealthRecordResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\LivestockHealthRecordResource;

class ViewLivestockHealthRecord extends ViewRecord
{
    protected static string $resource = LivestockHealthRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Event Overview')
                ->columns(3)
                ->schema([
                    TextEntry::make('livestockBatch.batch_reference')
                        ->label('Batch Reference')
                        ->badge()
                        ->color('gray')
                        ->copyable(),

                    TextEntry::make('livestockBatch.farm.name')
                        ->label('Farm'),

                    TextEntry::make('event_type')
                        ->badge()
                        ->color('primary')
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),

                    TextEntry::make('event_date')
                        ->date('d M Y'),

                    TextEntry::make('administered_by')
                        ->placeholder('—'),

                    TextEntry::make('cost')
                        ->money('GHS')
                        ->color('warning'),
                ]),

            Section::make('Treatment Information')
                ->columns(2)
                ->schema([
                    TextEntry::make('medicine_used')->label('Medicine Used')->placeholder('—'),
                    TextEntry::make('dosage')->placeholder('—'),

                    TextEntry::make('next_due_date')
                        ->date('d M Y')
                        ->label('Next Due Date')
                        ->placeholder('Not scheduled')
                        ->color(fn ($state) => $state && now()->gte($state) ? 'danger' : 'success'),
                ]),

            Section::make('Description')
                ->schema([
                    TextEntry::make('description')->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('notes')->columnSpanFull()->placeholder('No notes'),
                ]),

            Section::make('Audit')
                ->columns(2)
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')->dateTime()->label('Created'),
                    TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                ]),
        ]);
    }
}