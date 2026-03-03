<?php

namespace Modules\Construction\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Construction\Filament\Resources\MonitoringReportResource\Pages;
use Modules\Construction\Models\Contractor;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\MonitoringReport;
use Modules\Construction\Models\ProjectPhase;
use Modules\Construction\Models\SiteMonitor;

class MonitoringReportResource extends Resource
{
    protected static ?string $model = MonitoringReport::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Site Reports';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Report Details')->schema([
                Grid::make(2)->schema([
                    Select::make('site_monitor_id')
                        ->label('Monitor')
                        ->options(fn () => SiteMonitor::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),
                    Select::make('construction_project_id')
                        ->label('Project')
                        ->options(fn () => ConstructionProject::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    Select::make('project_phase_id')
                        ->label('Phase')
                        ->options(fn () => ProjectPhase::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->nullable(),
                    Select::make('contractor_id')
                        ->label('Contractor')
                        ->options(fn () => Contractor::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->nullable(),
                ]),
                Grid::make(2)->schema([
                    DatePicker::make('visit_date')->required(),
                    DatePicker::make('report_date')->required()->default(now()),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('weather_conditions')->maxLength(100),
                    TextInput::make('workers_on_site')->numeric()->minValue(0),
                ]),
                TextInput::make('compliance_score')
                    ->label('Compliance Score (0-100)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Textarea::make('findings')->required()->rows(4)->columnSpanFull(),
                Textarea::make('recommendations')->rows(3)->columnSpanFull(),
                FileUpload::make('attachment_paths')
                    ->label('Attachments')
                    ->multiple()
                    ->disk('public')
                    ->directory('monitoring-reports')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(array_combine(
                        MonitoringReport::STATUSES,
                        array_map('ucfirst', MonitoringReport::STATUSES)
                    ))
                    ->default('draft')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('monitor.name')->label('Monitor')->searchable()->sortable(),
                TextColumn::make('project.name')->label('Project')->searchable(),
                TextColumn::make('visit_date')->date()->sortable(),
                TextColumn::make('contractor.name')->label('Contractor')->placeholder('—'),
                TextColumn::make('compliance_score')
                    ->label('Compliance')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null   => 'gray',
                        $state >= 80      => 'success',
                        $state >= 60      => 'warning',
                        default           => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '/100' : '—'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'info',
                        'reviewed'  => 'warning',
                        'actioned'  => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(array_combine(
                        MonitoringReport::STATUSES,
                        array_map('ucfirst', MonitoringReport::STATUSES)
                    )),
                SelectFilter::make('construction_project_id')
                    ->label('Project')
                    ->relationship('project', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                TableAction::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'submitted'])),
                TableAction::make('mark_reviewed')
                    ->label('Mark Reviewed')
                    ->icon('heroicon-o-check')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'submitted')
                    ->action(fn ($record) => $record->update(['status' => 'reviewed'])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('visit_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMonitoringReports::route('/'),
            'create' => Pages\CreateMonitoringReport::route('/create'),
            'view'   => Pages\ViewMonitoringReport::route('/{record}'),
            'edit'   => Pages\EditMonitoringReport::route('/{record}/edit'),
        ];
    }
}
