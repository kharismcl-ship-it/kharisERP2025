<?php

namespace Modules\HR\Filament\Resources\EmployeeSalaryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\EmployeeSalaryResource;

class ViewEmployeeSalary extends ViewRecord
{
    protected static string $resource = EmployeeSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Salary Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('employee.full_name')
                            ->label('Employee')
                            ->getStateUsing(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                            ->weight('bold'),
                        TextEntry::make('company.name')->label('Company'),
                        TextEntry::make('basic_salary')->money('GHS')->label('Basic Salary'),
                        TextEntry::make('effective_date')->date()->label('Effective From'),
                        TextEntry::make('salaryScale.name')->label('Salary Scale')->placeholder('—'),
                        TextEntry::make('currency')->label('Currency')->default('GHS'),
                    ]),

                Section::make('Audit')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')->dateTime()->label('Created'),
                        TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                    ]),
            ]);
    }
}