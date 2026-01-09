<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\EmploymentContractResource\Pages;
use Modules\HR\Models\EmploymentContract;

class EmploymentContractResource extends Resource
{
    protected static ?string $model = EmploymentContract::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->required(),
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),
                Forms\Components\TextInput::make('contract_number')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->nullable(),
                Forms\Components\Select::make('contract_type')
                    ->options([
                        'permanent' => 'Permanent',
                        'fixed_term' => 'Fixed Term',
                        'casual' => 'Casual',
                    ])
                    ->required()
                    ->default('permanent'),
                Forms\Components\DatePicker::make('probation_end_date')
                    ->nullable(),
                Forms\Components\Toggle::make('is_current')
                    ->required(),
                Forms\Components\TextInput::make('basic_salary')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(3)
                    ->default('GHS'),
                Forms\Components\TextInput::make('working_hours_per_week')
                    ->numeric()
                    ->nullable(),
                Forms\Components\Textarea::make('notes')
                    ->nullable(),
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
                Tables\Columns\TextColumn::make('contract_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contract_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('probation_end_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_current')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('working_hours_per_week')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('contract_type')
                    ->options([
                        'permanent' => 'Permanent',
                        'fixed_term' => 'Fixed Term',
                        'casual' => 'Casual',
                    ]),
                Tables\Filters\TernaryFilter::make('is_current'),
            ])
            ->actions([
                EditAction::make(),
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
            'index' => Pages\ListEmploymentContracts::route('/'),
            'create' => Pages\CreateEmploymentContract::route('/create'),
            'edit' => Pages\EditEmploymentContract::route('/{record}/edit'),
        ];
    }
}
