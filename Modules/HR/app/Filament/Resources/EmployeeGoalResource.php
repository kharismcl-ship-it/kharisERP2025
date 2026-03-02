<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\EmployeeGoalResource\Pages;
use Modules\HR\Models\EmployeeGoal;

class EmployeeGoalResource extends Resource
{
    protected static ?string $model = EmployeeGoal::class;

    /**
     * This model has no direct company_id — Filament's ownership
     * check is skipped. Data isolation is handled via the parent
     * relationship or a custom getEloquentQuery() scope.
     */
    protected static bool $isScopedToTenant = false;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static string|\UnitEnum|null $navigationGroup = 'Performance';

    protected static ?int $navigationSort = 62;

    protected static ?string $navigationLabel = 'Employee Goals';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Goal Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('performance_cycle_id')
                            ->label('Performance Cycle')
                            ->relationship('performanceCycle', 'name')
                            ->searchable()->preload()->nullable(),
                        Forms\Components\TextInput::make('title')->required()->maxLength(200)->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options(EmployeeGoal::STATUSES)
                            ->required()->native(false),
                        Forms\Components\Select::make('priority')
                            ->options(EmployeeGoal::PRIORITIES)
                            ->required()->native(false),
                        Forms\Components\DatePicker::make('due_date')->native(false)->nullable(),
                    ]),

                Section::make('Target & Progress')
                    ->collapsible()
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('target_value')
                            ->numeric()->nullable(),
                        Forms\Components\TextInput::make('actual_value')
                            ->numeric()->nullable(),
                        Forms\Components\TextInput::make('unit_of_measure')
                            ->placeholder('e.g. %, count, GHS')->nullable(),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->getStateUsing(fn ($r) => $r->employee->first_name . ' ' . $r->employee->last_name)
                    ->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high'   => 'danger',
                        'medium' => 'warning',
                        'low'    => 'gray',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'not_started' => 'gray',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => EmployeeGoal::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('completion_pct')
                    ->label('Progress')
                    ->getStateUsing(fn (EmployeeGoal $r) => $r->completion_percentage . '%')
                    ->badge()
                    ->color(fn (EmployeeGoal $r): string => $r->completion_percentage >= 100 ? 'success' : ($r->completion_percentage >= 50 ? 'warning' : 'gray')),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(EmployeeGoal::STATUSES),
                Tables\Filters\SelectFilter::make('priority')->options(EmployeeGoal::PRIORITIES),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('markComplete')
                        ->label('Mark Complete')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (EmployeeGoal $r) => $r->status === 'in_progress')
                        ->action(function (EmployeeGoal $record) {
                            $record->update(['status' => 'completed', 'actual_value' => $record->target_value]);
                            Notification::make()->title('Goal marked as completed')->success()->send();
                        }),
                    Action::make('startProgress')
                        ->label('Start Progress')
                        ->icon('heroicon-o-play')
                        ->color('warning')
                        ->visible(fn (EmployeeGoal $r) => $r->status === 'not_started')
                        ->action(function (EmployeeGoal $record) {
                            $record->update(['status' => 'in_progress']);
                            Notification::make()->title('Goal in progress')->warning()->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmployeeGoals::route('/'),
            'create' => Pages\CreateEmployeeGoal::route('/create'),
            'view'   => Pages\ViewEmployeeGoal::route('/{record}'),
            'edit'   => Pages\EditEmployeeGoal::route('/{record}/edit'),
        ];
    }
}