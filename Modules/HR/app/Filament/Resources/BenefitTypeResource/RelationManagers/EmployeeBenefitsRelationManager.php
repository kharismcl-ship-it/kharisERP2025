<?php

namespace Modules\HR\Filament\Resources\BenefitTypeResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Models\EmployeeBenefit;

class EmployeeBenefitsRelationManager extends RelationManager
{
    protected static string $relationship = 'employeeBenefits';

    protected static ?string $title = 'Enrolled Employees';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable()->preload()->required(),
                Forms\Components\Select::make('status')
                    ->options(EmployeeBenefit::STATUSES)
                    ->required()->native(false),
                Forms\Components\DatePicker::make('start_date')->required()->native(false),
                Forms\Components\DatePicker::make('end_date')->nullable()->native(false),
                Forms\Components\TextInput::make('employer_contribution_override')
                    ->label('Employer Contrib. Override')->numeric()->prefix('GHS')->nullable(),
                Forms\Components\TextInput::make('employee_contribution_override')
                    ->label('Employee Contrib. Override')->numeric()->prefix('GHS')->nullable(),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->getStateUsing(fn ($r) => $r->employee->first_name . ' ' . $r->employee->last_name)
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success', 'pending' => 'warning', 'inactive' => 'gray', default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date()->placeholder('Ongoing'),
                Tables\Columns\TextColumn::make('employer_contribution_override')
                    ->label('Emp. Contrib.')->money('GHS')->placeholder('Default'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}