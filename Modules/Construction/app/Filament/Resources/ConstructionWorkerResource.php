<?php

namespace Modules\Construction\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Construction\Filament\Resources\ConstructionWorkerResource\Pages;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\ConstructionWorker;
use Modules\Construction\Models\Contractor;
use Modules\HR\Models\Employee;

class ConstructionWorkerResource extends Resource
{
    protected static ?string $model = ConstructionWorker::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Workers';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Worker Details')->schema([
                Grid::make(2)->schema([
                    Select::make('construction_project_id')
                        ->label('Project')
                        ->options(fn () => ConstructionProject::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload(),
                    Select::make('category')
                        ->options(array_combine(
                            ConstructionWorker::CATEGORIES,
                            array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ConstructionWorker::CATEGORIES))
                        ))
                        ->default('day_labour')
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('trade')->maxLength(255)->placeholder('Carpenter, Mason, etc.'),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('phone')->tel()->maxLength(20),
                    TextInput::make('email')->email()->maxLength(255),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('national_id')->label('National ID')->maxLength(50),
                    TextInput::make('daily_rate')->numeric()->prefix('GHS')->step(0.01)->default(0),
                ]),
                Grid::make(2)->schema([
                    Select::make('contractor_id')
                        ->label('Contractor')
                        ->options(fn () => Contractor::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->nullable(),
                    Select::make('employee_id')
                        ->label('Linked Employee')
                        ->options(fn () => Employee::all()->pluck('full_name', 'id')->toArray())
                        ->searchable()
                        ->nullable(),
                ]),
            ]),

            Section::make('Contract')->schema([
                Grid::make(3)->schema([
                    DatePicker::make('contract_start')->label('Start Date'),
                    DatePicker::make('contract_end')->label('End Date'),
                    Select::make('status')
                        ->options(array_combine(
                            ConstructionWorker::STATUSES,
                            array_map('ucfirst', ConstructionWorker::STATUSES)
                        ))
                        ->default('active')
                        ->required(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')->label('Project')->searchable()->sortable()->placeholder('—'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'project_staff'  => 'info',
                        'subcontractor'  => 'warning',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('trade')->placeholder('—'),
                TextColumn::make('phone')->placeholder('—'),
                TextColumn::make('daily_rate')->money('GHS')->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'inactive'  => 'gray',
                        'suspended' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(array_combine(
                        ConstructionWorker::STATUSES,
                        array_map('ucfirst', ConstructionWorker::STATUSES)
                    )),
                SelectFilter::make('category')
                    ->options(array_combine(
                        ConstructionWorker::CATEGORIES,
                        array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ConstructionWorker::CATEGORIES))
                    )),
                SelectFilter::make('construction_project_id')
                    ->label('Project')
                    ->relationship('project', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConstructionWorkers::route('/'),
            'create' => Pages\CreateConstructionWorker::route('/create'),
            'view'   => Pages\ViewConstructionWorker::route('/{record}'),
            'edit'   => Pages\EditConstructionWorker::route('/{record}/edit'),
        ];
    }
}
