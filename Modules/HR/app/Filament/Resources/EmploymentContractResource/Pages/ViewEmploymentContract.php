<?php

namespace Modules\HR\Filament\Resources\EmploymentContractResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\EmploymentContractResource;

class ViewEmploymentContract extends ViewRecord
{
    protected static string $resource = EmploymentContractResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contract Details')->columns(2)->schema([
                TextEntry::make('employee_name')->label('Employee')
                    ->getStateUsing(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                    ->weight('bold'),
                TextEntry::make('contract_number')->label('Contract No.')->placeholder('—'),
                TextEntry::make('company.name')->label('Company'),
                TextEntry::make('contract_type')->label('Type')
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state ?? ''))),
                TextEntry::make('start_date')->date()->label('Start Date'),
                TextEntry::make('end_date')->date()->label('End Date')->placeholder('Indefinite'),
                TextEntry::make('probation_end_date')->date()->label('Probation Ends')->placeholder('—'),
                TextEntry::make('basic_salary')->money('GHS')->label('Basic Salary')->placeholder('—'),
                TextEntry::make('working_hours_per_week')->label('Hours/Week')->suffix(' hrs')->placeholder('—'),
                TextEntry::make('currency')->default('GHS'),
                IconEntry::make('is_current')->label('Current Contract')->boolean(),
            ]),
            Section::make('Notes')->collapsible()->schema([
                TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}