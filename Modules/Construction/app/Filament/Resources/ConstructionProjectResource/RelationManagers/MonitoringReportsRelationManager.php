<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Construction\Models\MonitoringReport;
use Modules\Construction\Models\SiteMonitor;

class MonitoringReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'monitoringReports';

    protected static ?string $title = 'Site Reports';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('site_monitor_id')
                ->label('Monitor')
                ->options(fn () => SiteMonitor::pluck('name', 'id')->toArray())
                ->searchable()
                ->required(),
            DatePicker::make('visit_date')->required(),
            DatePicker::make('report_date')->required()->default(now()),
            TextInput::make('compliance_score')->numeric()->minValue(0)->maxValue(100),
            Textarea::make('findings')->required()->rows(3)->columnSpanFull(),
            Select::make('status')
                ->options(array_combine(
                    MonitoringReport::STATUSES,
                    array_map('ucfirst', MonitoringReport::STATUSES)
                ))
                ->default('draft')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('monitor.name')->label('Monitor'),
                TextColumn::make('visit_date')->date()->sortable(),
                TextColumn::make('compliance_score')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 80   => 'success',
                        $state >= 60   => 'warning',
                        default        => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '/100' : '—'),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
            ])
            ->headerActions([\Filament\Tables\Actions\CreateAction::make()])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ])])
            ->defaultSort('visit_date', 'desc');
    }
}
