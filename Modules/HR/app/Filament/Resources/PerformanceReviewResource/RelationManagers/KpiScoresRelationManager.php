<?php

namespace Modules\HR\Filament\Resources\PerformanceReviewResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Models\KpiDefinition;

class KpiScoresRelationManager extends RelationManager
{
    protected static string $relationship = 'kpiScores';

    protected static ?string $title = 'KPI Scores';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('kpi_definition_id')
                    ->label('KPI')
                    ->options(function (): array {
                        $review = $this->getOwnerRecord();
                        return KpiDefinition::where('company_id', $review->company_id)
                            ->where('is_active', true)
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->required()
                    ->searchable()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('target_value')
                    ->numeric()
                    ->nullable(),

                Forms\Components\TextInput::make('actual_value')
                    ->numeric()
                    ->nullable()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $get, Forms\Set $set) {
                        $target = (float) $get('target_value');
                        if ($target > 0 && $state !== null) {
                            $set('score', round(min(((float) $state / $target) * 100, 100), 2));
                        }
                    }),

                Forms\Components\TextInput::make('score')
                    ->label('Score (0-100)')
                    ->numeric()
                    ->suffix('%')
                    ->nullable(),

                Forms\Components\Textarea::make('notes')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kpiDefinition.name')
                    ->label('KPI')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_value')
                    ->label('Target')
                    ->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('actual_value')
                    ->label('Actual')
                    ->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score %')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default      => 'danger',
                    }),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
