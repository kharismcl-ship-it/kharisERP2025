<?php

namespace Modules\ManufacturingPaper\Filament\Resources\Staff\MyProductionBatchResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\ManufacturingPaper\Models\MpProductionBatch;

class ProductionBatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Production Batch')
                ->columns(3)
                ->schema([
                    TextEntry::make('batch_number')->label('Batch #')->badge()->color('gray'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'completed'   => 'success',
                            'in_progress' => 'warning',
                            'planned'     => 'info',
                            'on_hold'     => 'gray',
                            'cancelled'   => 'danger',
                            default       => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),
                    TextEntry::make('plant.name')->label('Plant')->placeholder('—'),
                    TextEntry::make('paperGrade.name')->label('Paper Grade')->placeholder('—'),
                    TextEntry::make('quantity_planned')
                        ->label('Planned Qty')
                        ->suffix(fn ($record) => ' ' . $record->unit),
                    TextEntry::make('quantity_produced')
                        ->label('Produced Qty')
                        ->suffix(fn ($record) => ' ' . $record->unit)
                        ->placeholder('—'),
                    TextEntry::make('efficiency_percent')->label('Efficiency')->suffix('%')->placeholder('—'),
                    TextEntry::make('start_time')->dateTime()->label('Started')->placeholder('—'),
                    TextEntry::make('end_time')->dateTime()->label('Ended')->placeholder('—'),
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }
}
