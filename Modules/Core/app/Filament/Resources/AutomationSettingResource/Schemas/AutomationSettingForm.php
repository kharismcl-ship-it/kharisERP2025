<?php

namespace Modules\Core\Filament\Resources\AutomationSettingResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Modules\Core\Services\AutomationService;

class AutomationSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        $automationService = app(AutomationService::class);
        $availableModules = ['HR' => 'Human Resources'];
        $availableActions = [
            'HR' => [
                'leave_accrual' => 'Leave Balance Accrual',
            ],
        ];

        return $schema
            ->components([
                Forms\Components\Select::make('module')
                    ->required()
                    ->options($availableModules)
                    ->reactive()
                    ->afterStateUpdated(function (Set $set) {
                        $set('action', null);
                    }),

                Forms\Components\Select::make('action')
                    ->required()
                    ->options(function (Get $get) use ($availableActions) {
                        $module = $get('module');

                        return $availableActions[$module] ?? [];
                    })
                    ->disabled(fn (Get $get) => ! $get('module')),

                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Toggle::make('is_enabled')
                    ->default(true)
                    ->required(),

                Forms\Components\Select::make('schedule_type')
                    ->required()
                    ->options([
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'yearly' => 'Yearly',
                    ])
                    ->default('monthly'),

                Forms\Components\Select::make('schedule_value')
                    ->required()
                    ->options(function (Get $get) {
                        $type = $get('schedule_type');

                        switch ($type) {
                            case 'monthly':
                                return array_combine(range(1, 28), range(1, 28));
                            case 'quarterly':
                                return [
                                    1 => 'Q1 (Jan-Mar)',
                                    2 => 'Q2 (Apr-Jun)',
                                    3 => 'Q3 (Jul-Sep)',
                                    4 => 'Q4 (Oct-Dec)',
                                ];
                            case 'yearly':
                                return [1 => 'January 1st'];
                            default:
                                return [1 => 'Day 1'];
                        }
                    })
                    ->default(1)
                    ->disabled(fn (Get $get) => ! $get('schedule_type')),

                Forms\Components\DateTimePicker::make('last_run_at')
                    ->disabled()
                    ->displayFormat('M j, Y H:i'),

                Forms\Components\DateTimePicker::make('next_run_at')
                    ->required()
                    ->displayFormat('M j, Y H:i'),

                Forms\Components\KeyValue::make('config')
                    ->keyLabel('Setting')
                    ->valueLabel('Value')
                    ->default([
                        'accrual_multiplier' => 1.0,
                        'skip_inactive_employees' => true,
                    ]),

            ]);
    }
}
