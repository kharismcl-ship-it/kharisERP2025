<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Clusters\HrPerformanceCluster;
use Modules\HR\Filament\Resources\KpiScoreResource\Pages;
use Modules\HR\Models\KpiScore;

class KpiScoreResource extends Resource
{
    protected static ?string $cluster = HrPerformanceCluster::class;
    protected static ?string $model = KpiScore::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?int $navigationSort = 57;

    protected static ?string $navigationLabel = 'KPI Scores';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('KPI Score')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('performance_review_id')
                            ->label('Performance Review')
                            ->relationship('performanceReview', 'id')
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('kpi_definition_id')
                            ->label('KPI Definition')
                            ->relationship('kpiDefinition', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\TextInput::make('target_value')
                            ->numeric()->required(),
                        Forms\Components\TextInput::make('actual_value')
                            ->numeric()->required(),
                        Forms\Components\TextInput::make('score')
                            ->numeric()->readOnly()
                            ->helperText('Auto-computed from actual vs target'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('performanceReview.id')
                    ->label('Review ID')->sortable(),
                Tables\Columns\TextColumn::make('kpiDefinition.name')
                    ->label('KPI')->searchable(),
                Tables\Columns\TextColumn::make('target_value')
                    ->numeric(decimalPlaces: 2)->sortable(),
                Tables\Columns\TextColumn::make('actual_value')
                    ->numeric(decimalPlaces: 2)->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKpiScores::route('/'),
            'create' => Pages\CreateKpiScore::route('/create'),
            'edit'   => Pages\EditKpiScore::route('/{record}/edit'),
        ];
    }
}
