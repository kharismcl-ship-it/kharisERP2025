<?php

namespace Modules\HR\Filament\Resources\Staff\MyTrainingResource\Schemas;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Models\TrainingProgram;

class TrainingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Training Request')
                ->schema([
                    Forms\Components\Select::make('training_program_id')
                        ->label('Training Program')
                        ->options(function () {
                            $companyId = Filament::getTenant()?->id;
                            return TrainingProgram::where('company_id', $companyId)
                                ->whereIn('status', ['planned', 'ongoing'])
                                ->pluck('title', 'id');
                        })
                        ->required()
                        ->native(false),
                    Forms\Components\Textarea::make('notes')
                        ->label('Reason / Notes')
                        ->rows(3),
                ]),
        ]);
    }
}
