<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmWorkerResource\Pages;
use Modules\Farms\Models\FarmWorker;

class FarmWorkerResource extends Resource
{
    protected static ?string $model = FarmWorker::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Farm Workers';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Worker Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('employee_id')
                        ->label('HR Employee (optional)')
                        ->relationship('employee', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name ?? ($record->first_name . ' ' . $record->last_name))
                        ->searchable()
                        ->nullable()
                        ->helperText('Link to an HR employee record, or leave blank for casual workers.'),

                    TextInput::make('name')
                        ->label('Worker Name')
                        ->maxLength(255)
                        ->helperText('Required if not linked to an HR employee.')
                        ->requiredWithout('employee_id'),

                    Select::make('role')
                        ->options(array_combine(
                            FarmWorker::ROLES,
                            array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), FarmWorker::ROLES))
                        ))
                        ->default('labourer'),

                    TextInput::make('daily_rate')
                        ->label('Daily Rate (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([Textarea::make('notes')->rows(2)->columnSpanFull()]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label('Name')
                    ->getStateUsing(fn ($record) => $record->display_name)
                    ->searchable(query: fn ($query, $search) =>
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhereHas('employee', fn ($q) =>
                                $q->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                            )
                    ),

                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color('primary'),

                TextColumn::make('farm.name')->label('Farm')->sortable(),

                TextColumn::make('employee.job_title')
                    ->label('HR Job Title')
                    ->placeholder('—'),

                TextColumn::make('daily_rate')
                    ->money('GHS')
                    ->label('Daily Rate')
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('tasks_count')
                    ->label('Open Tasks')
                    ->getStateUsing(fn ($record) => $record->tasks()->whereNull('completed_at')->count())
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('role')
                    ->options(array_combine(
                        FarmWorker::ROLES,
                        array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), FarmWorker::ROLES))
                    )),
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmWorkers::route('/'),
            'create' => Pages\CreateFarmWorker::route('/create'),
            'view'   => Pages\ViewFarmWorker::route('/{record}'),
            'edit'   => Pages\EditFarmWorker::route('/{record}/edit'),
        ];
    }
}
