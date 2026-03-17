<?php

namespace Modules\HR\Filament\Resources\Staff\MyTrainingResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Models\TrainingNomination;

class TrainingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Training Nomination')
                ->columns(2)
                ->schema([
                    TextEntry::make('trainingProgram.title')
                        ->label('Program')
                        ->columnSpanFull(),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'nominated' => 'warning',
                            'confirmed' => 'info',
                            'attended'  => 'primary',
                            'completed' => 'success',
                            'cancelled' => 'gray',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => TrainingNomination::STATUSES[$state] ?? ucfirst($state ?? '')),
                    TextEntry::make('trainingProgram.start_date')
                        ->label('Start Date')
                        ->date()
                        ->placeholder('TBD'),
                    TextEntry::make('trainingProgram.end_date')
                        ->label('End Date')
                        ->date()
                        ->placeholder('TBD'),
                    TextEntry::make('completion_date')
                        ->date()
                        ->placeholder('—'),
                    TextEntry::make('score')
                        ->suffix('/100')
                        ->placeholder('—'),
                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}
