<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpPlantResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ManufacturingPaper\Models\MpEquipmentLog;

class EquipmentLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'equipmentLogs';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('equipment_name')->required()->maxLength(255),
                Select::make('log_type')
                    ->options(array_combine(MpEquipmentLog::LOG_TYPES, array_map('ucfirst', MpEquipmentLog::LOG_TYPES)))
                    ->required(),
            ]),
            Textarea::make('description')->required()->rows(3)->columnSpanFull(),
            Grid::make(3)->schema([
                DateTimePicker::make('logged_at')->required()->default(now()),
                DateTimePicker::make('resolved_at')->nullable(),
                TextInput::make('cost')->numeric()->prefix('GHS')->step(0.01),
            ]),
            Select::make('status')
                ->options(array_combine(MpEquipmentLog::STATUSES, array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), MpEquipmentLog::STATUSES))))
                ->default('open'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('equipment_name')->label('Equipment')->searchable(),
                TextColumn::make('log_type')->label('Type')->badge(),
                TextColumn::make('logged_at')->dateTime()->sortable(),
                TextColumn::make('cost')->money('GHS'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'resolved'    => 'success',
                        'closed'      => 'gray',
                        'in_progress' => 'warning',
                        default       => 'danger',
                    }),
            ])
            ->defaultSort('logged_at', 'desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}