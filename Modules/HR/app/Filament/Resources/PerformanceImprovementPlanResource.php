<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Filament\Clusters\HrPerformanceCluster;
use Modules\HR\Models\Employee;
use Modules\HR\Models\PerformanceImprovementPlan;

class PerformanceImprovementPlanResource extends Resource
{
    protected static ?string $model = PerformanceImprovementPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Improvement Plans (PIP)';

    protected static ?string $cluster = HrPerformanceCluster::class;

    protected static ?int $navigationSort = 40;

    protected static ?string $slug = 'performance-improvement-plans';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', Filament::getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        $companyId = Filament::getTenant()?->id;

        return $schema->components([
            Section::make('Plan Details')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('reference')
                            ->label('Reference')
                            ->disabled()
                            ->placeholder('Auto-generated')
                            ->dehydrated(false),
                        Forms\Components\Select::make('status')
                            ->options(PerformanceImprovementPlan::STATUSES)
                            ->default('draft')
                            ->required(),
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->options(Employee::where('company_id', $companyId)
                                ->where('employment_status', 'active')
                                ->get()
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('manager_employee_id')
                            ->label('Manager')
                            ->options(Employee::where('company_id', $companyId)
                                ->where('employment_status', 'active')
                                ->get()
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\DatePicker::make('start_date')->required(),
                        Forms\Components\DatePicker::make('end_date')->required(),
                        Forms\Components\DatePicker::make('review_date')->nullable(),
                    ]),
                ]),

            Section::make('Performance Issue & Goals')
                ->schema([
                    Forms\Components\Textarea::make('performance_issue')
                        ->label('Performance Issue')
                        ->helperText('Describe the specific underperformance concern')
                        ->rows(3)
                        ->required(),
                    Forms\Components\Textarea::make('improvement_goals')
                        ->label('SMART Improvement Goals')
                        ->helperText('Specific, measurable targets for the employee')
                        ->rows(4)
                        ->required(),
                    Forms\Components\Textarea::make('support_provided')
                        ->label('Support / Resources')
                        ->helperText('Training, coaching, tools provided')
                        ->rows(2)
                        ->nullable(),
                    Forms\Components\Textarea::make('milestones')
                        ->label('Milestones / Checkpoints')
                        ->rows(2)
                        ->nullable(),
                ]),

            Section::make('Progress & Outcome')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('progress_notes')
                        ->label('Progress Notes')
                        ->rows(3)
                        ->columnSpanFull()
                        ->nullable(),
                    Forms\Components\Select::make('outcome')
                        ->options(PerformanceImprovementPlan::OUTCOMES)
                        ->nullable(),
                    Forms\Components\Textarea::make('outcome_notes')
                        ->label('Outcome Notes')
                        ->rows(2)
                        ->columnSpanFull()
                        ->nullable(),
                    Forms\Components\Toggle::make('employee_acknowledged')
                        ->label('Employee Acknowledged')
                        ->default(false),
                    Forms\Components\DateTimePicker::make('acknowledged_at')
                        ->nullable(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plan Summary')
                ->columns(3)
                ->schema([
                    TextEntry::make('reference')->badge()->color('gray'),
                    TextEntry::make('employee.full_name')->label('Employee'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'active'    => 'warning',
                            'completed' => 'success',
                            'escalated' => 'danger',
                            'cancelled' => 'gray',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => PerformanceImprovementPlan::STATUSES[$state] ?? ucfirst($state)),
                    TextEntry::make('start_date')->date(),
                    TextEntry::make('end_date')->date(),
                    TextEntry::make('outcome')
                        ->formatStateUsing(fn ($state) => PerformanceImprovementPlan::OUTCOMES[$state] ?? ucfirst((string)$state))
                        ->placeholder('—'),
                ]),
            Section::make('Detail')
                ->schema([
                    TextEntry::make('performance_issue')->placeholder('—'),
                    TextEntry::make('improvement_goals')->placeholder('—'),
                    TextEntry::make('support_provided')->placeholder('—'),
                    TextEntry::make('milestones')->placeholder('—'),
                    TextEntry::make('progress_notes')->placeholder('—'),
                    TextEntry::make('outcome_notes')->placeholder('—'),
                    IconEntry::make('employee_acknowledged')
                        ->label('Employee Acknowledged')
                        ->boolean(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->badge()->color('gray')->searchable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'warning',
                        'completed' => 'success',
                        'escalated' => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => PerformanceImprovementPlan::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('outcome')
                    ->formatStateUsing(fn ($state) => PerformanceImprovementPlan::OUTCOMES[$state] ?? ucfirst((string)$state))
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('employee_acknowledged')
                    ->label('Acknowledged')
                    ->boolean(),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(PerformanceImprovementPlan::STATUSES),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\HR\Filament\Resources\PerformanceImprovementPlanResource\Pages\ListPerformanceImprovementPlans::route('/'),
            'create' => \Modules\HR\Filament\Resources\PerformanceImprovementPlanResource\Pages\CreatePerformanceImprovementPlan::route('/create'),
            'edit'   => \Modules\HR\Filament\Resources\PerformanceImprovementPlanResource\Pages\EditPerformanceImprovementPlan::route('/{record}/edit'),
            'view'   => \Modules\HR\Filament\Resources\PerformanceImprovementPlanResource\Pages\ViewPerformanceImprovementPlan::route('/{record}'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Filament::getTenant()?->id;
        $data['created_by'] = auth()->id();
        return $data;
    }
}
