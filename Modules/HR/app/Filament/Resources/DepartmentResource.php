<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Filament\Clusters\HrSetupCluster;

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
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\DepartmentResource\Pages;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;

class DepartmentResource extends Resource
{
    protected static ?string $cluster = HrSetupCluster::class;
    protected static ?string $model = Department::class;

    protected static ?string $slug = 'departments';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;


    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'Departments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Department Info')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ,
                        Forms\Components\TextInput::make('name')
                            
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\Select::make('parent_id')
                            ->relationship('parent', 'name')
                            ->nullable(),
                        Forms\Components\Select::make('head_employee_id')
                            ->label('Department Head')
                            ->options(function (Forms\Get $get) {
                                $companyId = $get('company_id');
                                return Employee::when($companyId, fn ($q) => $q->where('company_id', $companyId))
                                    ->where('employment_status', 'active')
                                    ->get()
                                    ->mapWithKeys(fn ($e) => [$e->id => $e->first_name . ' ' . $e->last_name]);
                            })
                            ->searchable()
                            ->nullable(),
                    ]),

                \Filament\Schemas\Components\Section::make('Settings')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('company.name')
            ->groups([
                Group::make('company.name')
                    ->collapsible(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Department')
                    ->sortable(),
                Tables\Columns\TextColumn::make('head.full_name')
                    ->label('Head')
                    ->getStateUsing(fn (Department $record) => $record->head
                        ? $record->head->first_name . ' ' . $record->head->last_name
                        : '—')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
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
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('parent')
                    ->relationship('parent', 'name'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->slideOver(),
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
            \Modules\HR\Filament\Resources\DepartmentResource\RelationManagers\EmployeesRelationManager::class,
            \Modules\HR\Filament\Resources\DepartmentResource\RelationManagers\JobPositionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'view' => Pages\ViewDepartment::route('/{record}'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code'];
    }
}
