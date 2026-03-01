<?php

namespace Modules\HR\Filament\Resources\PayrollRunResource\RelationManagers;

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

class PayrollLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Payroll Lines';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable()->preload()->required(),
                Forms\Components\TextInput::make('basic_salary')
                    ->numeric()->prefix('GHS')->required(),
                Forms\Components\TextInput::make('total_allowances')
                    ->numeric()->prefix('GHS')->readOnly(),
                Forms\Components\TextInput::make('total_deductions')
                    ->numeric()->prefix('GHS')->readOnly(),
                Forms\Components\TextInput::make('paye_tax')
                    ->label('PAYE Tax')
                    ->numeric()->prefix('GHS'),
                Forms\Components\TextInput::make('ssnit_employee')
                    ->label('SSNIT (Employee)')
                    ->numeric()->prefix('GHS'),
                Forms\Components\TextInput::make('ssnit_employer')
                    ->label('SSNIT (Employer)')
                    ->numeric()->prefix('GHS'),
                Forms\Components\TextInput::make('gross_salary')
                    ->numeric()->prefix('GHS')->readOnly(),
                Forms\Components\TextInput::make('net_salary')
                    ->numeric()->prefix('GHS')->readOnly(),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name),
                Tables\Columns\TextColumn::make('basic_salary')->money('GHS')->sortable(),
                Tables\Columns\TextColumn::make('total_allowances')->label('Allowances')->money('GHS'),
                Tables\Columns\TextColumn::make('total_deductions')->label('Deductions')->money('GHS'),
                Tables\Columns\TextColumn::make('paye_tax')->label('PAYE')->money('GHS'),
                Tables\Columns\TextColumn::make('ssnit_employee')->label('SSNIT Emp.')->money('GHS'),
                Tables\Columns\TextColumn::make('gross_salary')->label('Gross')->money('GHS'),
                Tables\Columns\TextColumn::make('net_salary')
                    ->label('Net Pay')
                    ->money('GHS')
                    ->weight('bold'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
