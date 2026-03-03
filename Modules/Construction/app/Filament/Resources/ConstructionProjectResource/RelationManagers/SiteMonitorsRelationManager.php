<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Construction\Models\SiteMonitor;

class SiteMonitorsRelationManager extends RelationManager
{
    protected static string $relationship = 'siteMonitors';

    protected static ?string $title = 'Monitoring Team';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            Select::make('monitor_type')
                ->options([
                    'internal'   => 'Internal',
                    'external'   => 'External',
                    'consultant' => 'Consultant',
                ])
                ->default('internal')
                ->required(),
            Select::make('role')
                ->options([
                    'site_engineer'       => 'Site Engineer',
                    'quality_inspector'   => 'Quality Inspector',
                    'safety_officer'      => 'Safety Officer',
                    'independent_monitor' => 'Independent Monitor',
                    'other'               => 'Other',
                ])
                ->required(),
            TextInput::make('email')->email()->maxLength(255),
            TextInput::make('phone')->tel()->maxLength(20),
            DatePicker::make('appointed_date'),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('monitor_type')->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('role')->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('email')->placeholder('—'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->headerActions([\Filament\Tables\Actions\CreateAction::make()])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }
}
