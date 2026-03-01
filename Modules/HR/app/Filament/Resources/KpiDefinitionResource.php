<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
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
use Modules\HR\Filament\Resources\KpiDefinitionResource\Pages;
use Modules\HR\Models\KpiDefinition;

class KpiDefinitionResource extends Resource
{
    protected static ?string $model = KpiDefinition::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Performance';

    protected static ?int $navigationSort = 63;

    protected static ?string $navigationLabel = 'KPI Definitions';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('KPI Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\TextInput::make('name')->required()->maxLength(200),
                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()->preload()->nullable(),
                        Forms\Components\Select::make('job_position_id')
                            ->relationship('jobPosition', 'title')
                            ->searchable()->preload()->nullable(),
                        Forms\Components\TextInput::make('unit_of_measure')
                            ->placeholder('e.g. %, count, GHS, days')->nullable(),
                        Forms\Components\TextInput::make('target_value')->numeric()->nullable(),
                        Forms\Components\Select::make('frequency')
                            ->options(KpiDefinition::FREQUENCIES)
                            ->required()->native(false),
                        Forms\Components\Toggle::make('is_active')->default(true)->inline(false),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('department.name')->placeholder('All Depts.'),
                Tables\Columns\TextColumn::make('jobPosition.title')->label('Position')->placeholder('All Positions'),
                Tables\Columns\TextColumn::make('frequency')
                    ->badge()->color('primary')
                    ->formatStateUsing(fn ($state) => KpiDefinition::FREQUENCIES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('target_value')
                    ->label('Target')
                    ->getStateUsing(fn (KpiDefinition $r) => $r->target_value ? $r->target_value . ' ' . $r->unit_of_measure : '—'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('department')->relationship('department', 'name'),
                Tables\Filters\SelectFilter::make('frequency')->options(KpiDefinition::FREQUENCIES),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKpiDefinitions::route('/'),
            'create' => Pages\CreateKpiDefinition::route('/create'),
            'view'   => Pages\ViewKpiDefinition::route('/{record}'),
            'edit'   => Pages\EditKpiDefinition::route('/{record}/edit'),
        ];
    }
}
