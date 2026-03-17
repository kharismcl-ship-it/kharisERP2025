<?php

namespace Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Models\EmployeeGoal;

class EmployeeGoalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Goal')
                ->columns(3)
                ->schema([
                    TextEntry::make('title')
                        ->label('Goal Title')
                        ->columnSpanFull()
                        ->weight('bold'),

                    TextEntry::make('priority')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'high'   => 'danger',
                            'medium' => 'warning',
                            'low'    => 'gray',
                            default  => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => EmployeeGoal::PRIORITIES[$state] ?? ucfirst($state)),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'completed'   => 'success',
                            'in_progress' => 'warning',
                            'not_started' => 'gray',
                            'cancelled'   => 'danger',
                            default       => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => EmployeeGoal::STATUSES[$state] ?? ucfirst(str_replace('_', ' ', $state))),

                    TextEntry::make('performanceCycle.name')
                        ->label('Cycle')
                        ->placeholder('—'),

                    TextEntry::make('description')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),

            Section::make('Progress')
                ->columns(3)
                ->schema([
                    TextEntry::make('target_value')
                        ->label('Target')
                        ->suffix(fn ($record) => $record->unit_of_measure ? " {$record->unit_of_measure}" : '')
                        ->placeholder('—'),

                    TextEntry::make('actual_value')
                        ->label('Current')
                        ->suffix(fn ($record) => $record->unit_of_measure ? " {$record->unit_of_measure}" : '')
                        ->placeholder('—'),

                    TextEntry::make('completion_percentage')
                        ->label('Completion')
                        ->suffix('%')
                        ->color(fn ($state) => match (true) {
                            $state >= 80 => 'success',
                            $state >= 40 => 'warning',
                            default      => 'danger',
                        }),

                    TextEntry::make('due_date')
                        ->label('Target Date')
                        ->date()
                        ->placeholder('—'),

                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}
