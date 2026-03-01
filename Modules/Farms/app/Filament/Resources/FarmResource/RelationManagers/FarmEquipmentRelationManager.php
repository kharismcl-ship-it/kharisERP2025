<?php

namespace Modules\Farms\Filament\Resources\FarmResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Farms\Models\FarmEquipment;

class FarmEquipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'equipment';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            Select::make('equipment_type')
                ->options(array_combine(
                    FarmEquipment::EQUIPMENT_TYPES,
                    array_map('ucfirst', FarmEquipment::EQUIPMENT_TYPES)
                ))
                ->default('other')->required(),
            TextInput::make('make')->maxLength(100),
            TextInput::make('model')->maxLength(100),
            TextInput::make('year')->numeric()->minValue(1950)->maxValue(2099),
            Select::make('status')
                ->options(['active' => 'Active', 'maintenance' => 'In Maintenance', 'retired' => 'Retired'])
                ->default('active')->required(),
            DatePicker::make('purchase_date'),
            TextInput::make('purchase_price')->numeric()->prefix('GHS')->step(0.01),
            DatePicker::make('next_service_date'),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('equipment_type')->badge()->color('primary'),
                TextColumn::make('make'),
                TextColumn::make('year'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success', 'maintenance' => 'warning', 'retired' => 'danger', default => 'gray',
                    }),
                TextColumn::make('purchase_price')->money('GHS')->toggleable(),
                TextColumn::make('next_service_date')->date()->label('Next Service'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
