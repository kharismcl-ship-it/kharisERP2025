<?php

namespace Modules\Farms\Filament\Resources\CropScoutingResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\CropScoutingResource;
use Modules\Farms\Models\CropScoutingRecord;

class ViewCropScoutingRecord extends ViewRecord
{
    protected static string $resource = CropScoutingResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make(), DeleteAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Scouting Overview')
                ->columns(3)
                ->schema([
                    TextEntry::make('finding_type')
                        ->badge()
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                        ->color(fn (string $state): string => match ($state) {
                            'pest', 'disease' => 'danger',
                            'normal'          => 'success',
                            default           => 'warning',
                        }),

                    TextEntry::make('severity')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'critical' => 'danger',
                            'high'     => 'warning',
                            'medium'   => 'info',
                            'low'      => 'success',
                            default    => 'gray',
                        }),

                    TextEntry::make('scouting_date')->date('d M Y'),
                    TextEntry::make('cropCycle.crop_name')->label('Crop Cycle'),
                    TextEntry::make('farm.name')->label('Farm'),
                    TextEntry::make('scouted_by')->label('Scouted By')->placeholder('—'),
                ]),

            Section::make('Findings & Action')
                ->schema([
                    TextEntry::make('description')->columnSpanFull(),
                    TextEntry::make('recommended_action')->label('Recommended Action')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Follow-Up & Resolution')
                ->columns(2)
                ->schema([
                    TextEntry::make('follow_up_date')
                        ->date('d M Y')
                        ->label('Follow-Up Date')
                        ->placeholder('—')
                        ->color(fn ($state, $record) =>
                            $state && now()->gt($state) && ! $record->resolved_at ? 'danger' : null
                        ),

                    TextEntry::make('resolved_at')
                        ->label('Resolved At')
                        ->dateTime('d M Y H:i')
                        ->placeholder('Not yet resolved')
                        ->color(fn ($state) => $state ? 'success' : 'warning'),
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