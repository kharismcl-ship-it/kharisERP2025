<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Clusters\FarmOperationsCluster;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmTaskResource\Pages;
use Modules\Farms\Models\FarmTask;

class FarmTaskResource extends Resource
{
    protected static ?string $model = FarmTask::class;

    protected static ?string $cluster = FarmOperationsCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Farm Tasks';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Task Details')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),

                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('farm_plot_id')
                        ->label('Plot (optional)')
                        ->relationship('plot', 'name')
                        ->searchable()
                        ->nullable(),

                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle (optional)')
                        ->relationship('cropCycle', 'crop_name')
                        ->searchable()
                        ->nullable(),

                    Select::make('livestock_batch_id')
                        ->label('Livestock Batch (optional)')
                        ->relationship('livestockBatch', 'batch_reference')
                        ->searchable()
                        ->nullable(),

                    Select::make('assigned_to_worker_id')
                        ->label('Assign To')
                        ->relationship('assignedWorker', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                        ->searchable()
                        ->nullable(),

                    Select::make('task_type')
                        ->options(array_combine(
                            FarmTask::TASK_TYPES,
                            array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), FarmTask::TASK_TYPES))
                        ))
                        ->default('other'),

                    Select::make('priority')
                        ->options(array_combine(
                            FarmTask::PRIORITIES,
                            array_map('ucfirst', FarmTask::PRIORITIES)
                        ))
                        ->default('medium'),

                    DatePicker::make('due_date')->label('Due Date'),
                ]),

            Section::make('Description & Notes')
                ->schema([
                    Textarea::make('description')->rows(3)->columnSpanFull(),
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    }),

                TextColumn::make('title')->searchable()->limit(40),

                TextColumn::make('task_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color('primary'),

                TextColumn::make('farm.name')->label('Farm')->sortable(),

                TextColumn::make('assignedWorker.name')
                    ->label('Assigned To')
                    ->getStateUsing(fn ($record) => $record->assignedWorker?->display_name)
                    ->placeholder('—'),

                TextColumn::make('due_date')
                    ->date('d M Y')
                    ->label('Due')
                    ->sortable()
                    ->color(fn ($state, $record) =>
                        $state && now()->gt($state) && ! $record->completed_at ? 'danger' : null
                    ),

                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('d M Y')
                    ->placeholder('Open'),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('priority')
                    ->options(array_combine(FarmTask::PRIORITIES, array_map('ucfirst', FarmTask::PRIORITIES))),
                SelectFilter::make('task_type')
                    ->options(array_combine(
                        FarmTask::TASK_TYPES,
                        array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), FarmTask::TASK_TYPES))
                    )),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('complete')
                    ->label('Mark Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => ! $record->completed_at)
                    ->action(fn ($record) => $record->update(['completed_at' => now()])),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('due_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmTasks::route('/'),
            'create' => Pages\CreateFarmTask::route('/create'),
            'view'   => Pages\ViewFarmTask::route('/{record}'),
            'edit'   => Pages\EditFarmTask::route('/{record}/edit'),
        ];
    }
}