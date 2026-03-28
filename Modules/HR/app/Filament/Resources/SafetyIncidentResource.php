<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Clusters\HrSafetyCluster;
use Modules\HR\Filament\Resources\SafetyIncidentResource\Pages;
use Modules\HR\Models\Employee;
use Modules\HR\Models\SafetyIncident;

class SafetyIncidentResource extends Resource
{
    protected static ?string $cluster = HrSafetyCluster::class;
    protected static ?string $model = SafetyIncident::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;
    protected static ?int $navigationSort = 71;
    protected static ?string $navigationLabel = 'Incidents';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Incident Details')->columns(2)->schema([
                Forms\Components\TextInput::make('ref_number')->label('Ref #')
                    ->disabled()->dehydrated(false)->placeholder('Auto-generated'),
                Forms\Components\DateTimePicker::make('incident_date')->required()->default(now()),
                Forms\Components\TextInput::make('location')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('incident_type')
                    ->options(SafetyIncident::incidentTypes())->required(),
                Forms\Components\Select::make('severity')
                    ->options(SafetyIncident::severities())->required()->default('minor'),
                Forms\Components\Textarea::make('description')->rows(4)->required()->columnSpanFull(),
                Forms\Components\Select::make('employee_id')
                    ->label('Injured Employee')
                    ->relationship('employee', 'full_name')
                    ->searchable()->preload()->nullable(),
                Forms\Components\TextInput::make('injury_type')->maxLength(255),
                Forms\Components\TextInput::make('body_part_affected')->label('Body Part Affected')->maxLength(255),
                Forms\Components\Textarea::make('immediate_action_taken')->rows(3)->columnSpanFull(),
            ]),
            Section::make('Investigation')->columns(2)->schema([
                Forms\Components\Select::make('reported_by_employee_id')
                    ->label('Reported By')
                    ->relationship('reportedBy', 'full_name')
                    ->searchable()->preload()->nullable(),
                Forms\Components\Select::make('investigated_by_employee_id')
                    ->label('Investigated By')
                    ->relationship('investigatedBy', 'full_name')
                    ->searchable()->preload()->nullable(),
                Forms\Components\Select::make('status')
                    ->options([
                        'open'                => 'Open',
                        'under_investigation' => 'Under Investigation',
                        'closed'              => 'Closed',
                    ])->required()->default('open'),
                Forms\Components\Toggle::make('reported_to_authorities')->inline(false),
                Forms\Components\Textarea::make('root_cause')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('corrective_action')->rows(3)->columnSpanFull(),
                Forms\Components\DateTimePicker::make('closed_at'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('incident_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('ref_number')->label('Ref')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('incident_date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('location')->searchable()->limit(25),
                Tables\Columns\TextColumn::make('incident_type')->label('Type')
                    ->formatStateUsing(fn ($state) => SafetyIncident::incidentTypes()[$state] ?? $state)
                    ->badge()->color('warning'),
                Tables\Columns\TextColumn::make('severity')
                    ->badge()->color(fn ($state) => match ($state) {
                        'minor' => 'success', 'moderate' => 'warning',
                        'serious' => 'danger', 'critical', 'fatal' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()->color(fn ($state) => match ($state) {
                        'open' => 'danger', 'under_investigation' => 'warning', 'closed' => 'success', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('employee.full_name')->label('Injured Employee')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['open' => 'Open', 'under_investigation' => 'Under Investigation', 'closed' => 'Closed']),
                Tables\Filters\SelectFilter::make('severity')
                    ->options(SafetyIncident::severities()),
            ])
            ->actions([ActionGroup::make([ViewAction::make(), EditAction::make()])])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSafetyIncidents::route('/'),
            'create' => Pages\CreateSafetyIncident::route('/create'),
            'edit'   => Pages\EditSafetyIncident::route('/{record}/edit'),
            'view'   => Pages\ViewSafetyIncident::route('/{record}'),
        ];
    }
}