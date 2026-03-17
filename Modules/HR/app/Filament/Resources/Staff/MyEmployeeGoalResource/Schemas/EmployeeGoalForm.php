<?php

namespace Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Schemas;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Models\EmployeeGoal;
use Modules\HR\Models\PerformanceCycle;

class EmployeeGoalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Goal Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Goal Title')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull()
                        ->placeholder('e.g. Increase customer satisfaction score by 10%'),

                    Forms\Components\Select::make('priority')
                        ->options(EmployeeGoal::PRIORITIES)
                        ->default('medium')
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('performance_cycle_id')
                        ->label('Performance Cycle')
                        ->options(function () {
                            $companyId = Filament::getTenant()?->id;
                            return PerformanceCycle::where('company_id', $companyId)
                                ->orderByDesc('start_date')
                                ->pluck('name', 'id');
                        })
                        ->nullable()
                        ->native(false)
                        ->searchable()
                        ->placeholder('— optional —'),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('What does achieving this goal look like?'),
                ]),

            Section::make('Target & Progress')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('target_value')
                        ->label('Target Value')
                        ->numeric()
                        ->nullable()
                        ->placeholder('e.g. 100'),

                    Forms\Components\TextInput::make('unit_of_measure')
                        ->label('Unit')
                        ->maxLength(50)
                        ->nullable()
                        ->placeholder('e.g. %, calls, tasks'),

                    Forms\Components\TextInput::make('actual_value')
                        ->label('Current Progress')
                        ->numeric()
                        ->nullable()
                        ->placeholder('Update as you make progress'),

                    Forms\Components\Select::make('status')
                        ->options(EmployeeGoal::STATUSES)
                        ->default('not_started')
                        ->required()
                        ->native(false),

                    Forms\Components\DatePicker::make('due_date')
                        ->label('Target Date')
                        ->native(false)
                        ->nullable(),

                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('Any additional notes or context'),
                ]),
        ]);
    }
}
