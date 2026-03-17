<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmTaskResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FarmTaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Task Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('title')
                        ->columnSpanFull()
                        ->weight('bold'),

                    TextEntry::make('task_type')
                        ->label('Type')
                        ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                    TextEntry::make('priority')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'urgent' => 'danger',
                            'high'   => 'warning',
                            'medium' => 'info',
                            'low'    => 'gray',
                            default  => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst($state)),

                    TextEntry::make('due_date')
                        ->date()
                        ->placeholder('—'),

                    TextEntry::make('farm.name')
                        ->label('Farm')
                        ->placeholder('—'),

                    TextEntry::make('plot.name')
                        ->label('Plot')
                        ->placeholder('—'),

                    IconEntry::make('completed_at')
                        ->label('Completed')
                        ->boolean()
                        ->getStateUsing(fn ($record) => $record->completed_at !== null),

                    TextEntry::make('completed_at')
                        ->label('Completed At')
                        ->dateTime()
                        ->placeholder('Not yet completed'),

                    TextEntry::make('description')
                        ->columnSpanFull()
                        ->placeholder('—'),

                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}
