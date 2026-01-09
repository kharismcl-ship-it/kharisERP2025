<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

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

class EmploymentContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'contracts';

    protected static ?string $label = 'Employment Contracts';

    protected static ?string $title = 'Employment Contracts';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Columns\IconColumn::make('is_current')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('contract_type')
                    ->options([
                        'permanent' => 'Permanent',
                        'fixed_term' => 'Fixed Term',
                        'casual' => 'Casual',
                    ]),
                Tables\Filters\TernaryFilter::make('is_current'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
