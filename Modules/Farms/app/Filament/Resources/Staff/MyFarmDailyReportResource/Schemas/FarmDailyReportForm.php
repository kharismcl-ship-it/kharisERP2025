<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FarmDailyReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Daily Report')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('report_date')
                        ->required()
                        ->native(false)
                        ->default(today())
                        ->maxDate(today()),

                    Forms\Components\TextInput::make('weather_observation')
                        ->label('Weather Observation')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('summary')
                        ->label('Summary')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('activities_done')
                        ->label('Activities Done')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('issues_noted')
                        ->label('Issues Noted')
                        ->rows(2)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('recommendations')
                        ->label('Recommendations')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
