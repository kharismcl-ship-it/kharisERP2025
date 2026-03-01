<?php

namespace Modules\HR\Filament\Resources\EmployeeCompanyAssignmentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\EmployeeCompanyAssignmentResource;

class ViewEmployeeCompanyAssignment extends ViewRecord
{
    protected static string $resource = EmployeeCompanyAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Assignment Details')->columns(2)->schema([
                TextEntry::make('employee_name')->label('Employee')
                    ->getStateUsing(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                    ->weight('bold'),
                TextEntry::make('company.name')->label('Company'),
                TextEntry::make('role')->label('Role')->placeholder('—'),
                TextEntry::make('start_date')->date()->label('Start Date'),
                TextEntry::make('end_date')->date()->label('End Date')->placeholder('Ongoing'),
                TextEntry::make('assigned_at')->dateTime()->label('Assigned At')->placeholder('—'),
                TextEntry::make('expires_at')->dateTime()->label('Expires At')->placeholder('No Expiry'),
                IconEntry::make('is_active')->label('Active Assignment')->boolean(),
                TextEntry::make('assignment_reason')->label('Reason')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}