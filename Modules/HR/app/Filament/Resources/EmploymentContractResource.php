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
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\EmploymentContractResource\Pages;
use Modules\HR\Models\EmploymentContract;

class EmploymentContractResource extends Resource
{
    protected static ?string $cluster = HrRecordsCluster::class;
    protected static ?string $model = EmploymentContract::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;


    protected static ?int $navigationSort = 68;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Contract Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->required(),
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ,
                        Forms\Components\TextInput::make('contract_number')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\Select::make('contract_type')
                            ->options([
                                'permanent'  => 'Permanent',
                                'fixed_term' => 'Fixed Term',
                                'casual'     => 'Casual',
                            ])
                            ->required()
                            ->default('permanent'),
                    ]),

                \Filament\Schemas\Components\Section::make('Duration')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->nullable(),
                        Forms\Components\DatePicker::make('probation_end_date')
                            ->nullable(),
                        Forms\Components\Toggle::make('is_current')
                            ->required(),
                    ]),

                \Filament\Schemas\Components\Section::make('Compensation')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('basic_salary')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('currency')
                            ->maxLength(3)
                            ->default('GHS'),
                        Forms\Components\TextInput::make('working_hours_per_week')
                            ->numeric()
                            ->nullable(),
                    ]),

                \Filament\Schemas\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->nullable()
                            ->columnSpanFull(),
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
            'index' => Pages\ListEmploymentContracts::route('/'),
            'create' => Pages\CreateEmploymentContract::route('/create'),
            'view' => Pages\ViewEmploymentContract::route('/{record}'),
            'edit' => Pages\EditEmploymentContract::route('/{record}/edit'),
        ];
    }
}
