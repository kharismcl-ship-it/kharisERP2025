<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\HR\Models\SafetyIncident;

class MySafetyIncidentResource extends StaffSelfServiceResource
{
    protected static ?string $model = SafetyIncident::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;
    protected static ?string $navigationLabel = 'Report Incident';
    protected static string|\UnitEnum|null $navigationGroup = 'HR';
    protected static ?int $navigationSort = 65;
    protected static ?string $slug = 'my-safety-incidents';

    public static function getEloquentQuery(): Builder
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()
            ->where('company_id', $companyId)
            ->where('reported_by_employee_id', $employee->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Report Safety Incident')->columns(2)->schema([
                Forms\Components\DateTimePicker::make('incident_date')->required()->default(now())->columnSpanFull(),
                Forms\Components\TextInput::make('location')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('incident_type')
                    ->options(SafetyIncident::incidentTypes())->required(),
                Forms\Components\Select::make('severity')
                    ->options(SafetyIncident::severities())->required()->default('minor'),
                Forms\Components\Textarea::make('description')->rows(4)->required()->columnSpanFull(),
                Forms\Components\TextInput::make('injury_type')->maxLength(255)->placeholder('e.g. Cut, Strain'),
                Forms\Components\TextInput::make('body_part_affected')->maxLength(255),
                Forms\Components\Textarea::make('immediate_action_taken')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('incident_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('ref_number')->label('Ref')->sortable(),
                Tables\Columns\TextColumn::make('incident_date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('location')->limit(25),
                Tables\Columns\TextColumn::make('incident_type')->label('Type')
                    ->formatStateUsing(fn ($state) => SafetyIncident::incidentTypes()[$state] ?? $state)
                    ->badge()->color('warning'),
                Tables\Columns\TextColumn::make('severity')
                    ->badge()->color(fn ($state) => match ($state) {
                        'minor' => 'success', 'moderate' => 'warning',
                        'serious', 'critical', 'fatal' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()->color(fn ($state) => match ($state) {
                        'open' => 'danger', 'under_investigation' => 'warning', 'closed' => 'success', default => 'gray',
                    }),
            ])
            ->actions([\Filament\Actions\ViewAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\HR\Filament\Resources\Staff\MySafetyIncidentResource\Pages\ListMySafetyIncidents::route('/'),
            'create' => \Modules\HR\Filament\Resources\Staff\MySafetyIncidentResource\Pages\CreateMySafetyIncident::route('/create'),
            'view'   => \Modules\HR\Filament\Resources\Staff\MySafetyIncidentResource\Pages\ViewMySafetyIncident::route('/{record}'),
        ];
    }
}