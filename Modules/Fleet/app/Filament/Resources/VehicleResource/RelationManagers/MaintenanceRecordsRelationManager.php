<?php

namespace Modules\Fleet\Filament\Resources\VehicleResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Fleet\Models\MaintenanceRecord;

class MaintenanceRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceRecords';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->options(array_combine(
                    MaintenanceRecord::TYPES,
                    array_map('ucwords', array_map(fn ($t) => str_replace('_', ' ', $t), MaintenanceRecord::TYPES))
                ))
                ->required(),
            Textarea::make('description')->required()->columnSpanFull(),
            DatePicker::make('service_date')->required(),
            TextInput::make('mileage_at_service')->label('Mileage at Service')->numeric()->step(0.01)->suffix('km'),
            DatePicker::make('next_service_date')->label('Next Service Date'),
            TextInput::make('next_service_mileage')->label('Next Service Mileage')->numeric()->step(0.01)->suffix('km'),
            TextInput::make('service_provider')->label('Service Provider')->maxLength(255),
            TextInput::make('cost')->numeric()->prefix('GHS')->step(0.01),
            Select::make('status')
                ->options([
                    'scheduled'   => 'Scheduled',
                    'in_progress' => 'In Progress',
                    'completed'   => 'Completed',
                ])
                ->default('scheduled'),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service_date')->date()->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('description')->limit(40),
                TextColumn::make('cost')->money('GHS')->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'scheduled'   => 'info',
                        default       => 'gray',
                    }),
                TextColumn::make('service_provider'),
                TextColumn::make('next_service_date')->date()->label('Next Service'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('service_date', 'desc');
    }
}
