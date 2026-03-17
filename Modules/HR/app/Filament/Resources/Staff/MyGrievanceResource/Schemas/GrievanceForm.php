<?php

namespace Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GrievanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Grievance Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('grievance_type')
                        ->label('Type of Grievance')
                        ->options([
                            'workplace_harassment' => 'Workplace Harassment',
                            'discrimination'       => 'Discrimination',
                            'unfair_treatment'     => 'Unfair Treatment',
                            'pay_dispute'          => 'Pay Dispute',
                            'working_conditions'   => 'Working Conditions',
                            'management_conduct'   => 'Management Conduct',
                            'other'                => 'Other',
                        ])
                        ->required()
                        ->native(false),
                    Forms\Components\Toggle::make('is_anonymous')
                        ->label('File Anonymously')
                        ->helperText('Your name will not be shared with the respondent'),
                    Forms\Components\Textarea::make('description')
                        ->label('Describe Your Grievance')
                        ->required()
                        ->minLength(20)
                        ->maxLength(2000)
                        ->rows(6)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
