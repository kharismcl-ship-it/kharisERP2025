<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Filament\Clusters\HrRecordsCluster;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\EmployeeSalaryResource\Pages;
use Modules\HR\Models\EmployeeSalary;

class EmployeeSalaryResource extends Resource
{
    protected static ?string $cluster = HrRecordsCluster::class;
    protected static ?string $model = EmployeeSalary::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;


    protected static ?int $navigationSort = 53;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Salary Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->required(),
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ,
                        Forms\Components\Select::make('salary_scale_id')
                            ->relationship('salaryScale', 'name')
                            ->nullable(),
                        Forms\Components\TextInput::make('basic_salary')
                            ->required()
                            ->numeric(),
                    ]),

                Section::make('Dates & Currency')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('effective_from')
                            ->required(),
                        Forms\Components\DatePicker::make('effective_to')
                            ->nullable(),
                        Forms\Components\TextInput::make('currency')
                            ->required()
                            ->maxLength(3)
                            ->default('GHS'),
                        Forms\Components\Toggle::make('is_current')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('salaryScale.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('effective_from')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('effective_to')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_current')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee')
                    ->relationship('employee', 'first_name'),
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('salary_scale')
                    ->relationship('salaryScale', 'name'),
                Tables\Filters\TernaryFilter::make('is_current'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeSalaries::route('/'),
            'create' => Pages\CreateEmployeeSalary::route('/create'),
            'view' => Pages\ViewEmployeeSalary::route('/{record}'),
            'edit' => Pages\EditEmployeeSalary::route('/{record}/edit'),
        ];
    }
}
