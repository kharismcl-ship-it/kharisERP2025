<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Filament\Clusters\HrPerformanceCluster;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
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
use Modules\HR\Filament\Resources\PerformanceCycleResource\Pages;
use Modules\HR\Models\Employee;
use Modules\HR\Models\PerformanceCycle;
use Modules\HR\Models\PerformanceReview;

class PerformanceCycleResource extends Resource
{
    protected static ?string $cluster = HrPerformanceCluster::class;
    protected static ?string $model = PerformanceCycle::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPathRoundedSquare;


    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cycle Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ,
                        Forms\Components\TextInput::make('name')
                            
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('start_date')
                            ,
                        Forms\Components\DatePicker::make('end_date')
                            ,
                    ]),

                Section::make('Settings')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'planned' => 'Planned',
                                'open'    => 'Open',
                                'closed'  => 'Closed',
                            ])
                            ->required()
                            ->default('planned'),
                        Forms\Components\Textarea::make('description')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'    => 'success',
                        'planned' => 'info',
                        'closed'  => 'gray',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planned' => 'Planned',
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('createReviews')
                        ->label('Generate Reviews')
                        ->icon('heroicon-o-user-group')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Performance Reviews')
                        ->modalDescription('This will create a PerformanceReview record for every active employee in this company who does not already have one for this cycle. Continue?')
                        ->visible(fn (PerformanceCycle $r) => in_array($r->status, ['planned', 'open']))
                        ->action(function (PerformanceCycle $record) {
                            $employees = Employee::where('company_id', $record->company_id)
                                ->where('employment_status', 'active')
                                ->get();

                            $existingIds = PerformanceReview::where('performance_cycle_id', $record->id)
                                ->pluck('employee_id')
                                ->toArray();

                            $created = 0;
                            foreach ($employees as $employee) {
                                if (in_array($employee->id, $existingIds)) {
                                    continue;
                                }
                                PerformanceReview::create([
                                    'performance_cycle_id'  => $record->id,
                                    'employee_id'           => $employee->id,
                                    'reviewer_employee_id'  => $employee->reporting_to_employee_id,
                                    'status'                => 'pending',
                                ]);
                                $created++;
                            }

                            if ($record->status === 'planned') {
                                $record->update(['status' => 'open']);
                            }

                            Notification::make()
                                ->title("{$created} performance review(s) generated")
                                ->success()
                                ->send();
                        }),
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
            \Modules\HR\Filament\Resources\PerformanceCycleResource\RelationManagers\PerformanceReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPerformanceCycles::route('/'),
            'create' => Pages\CreatePerformanceCycle::route('/create'),
            'view'   => Pages\ViewPerformanceCycle::route('/{record}'),
            'edit'   => Pages\EditPerformanceCycle::route('/{record}/edit'),
        ];
    }
}
