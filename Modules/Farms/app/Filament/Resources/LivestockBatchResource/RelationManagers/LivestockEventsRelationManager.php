<?php

namespace Modules\Farms\Filament\Resources\LivestockBatchResource\RelationManagers;

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
use Modules\Farms\Models\LivestockEvent;

class LivestockEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_type')
                ->options(array_combine(
                    LivestockEvent::EVENT_TYPES,
                    array_map('ucfirst', LivestockEvent::EVENT_TYPES)
                ))
                ->required(),
            DatePicker::make('event_date')->required()->default(now()),
            TextInput::make('count')->label('Animals Involved')->integer()->default(1)->required(),
            TextInput::make('source_or_destination')->label('Supplier / Buyer')->maxLength(255),
            TextInput::make('unit_cost')->label('Unit Price (GHS)')->numeric()->step(0.0001)->prefix('GHS'),
            TextInput::make('total_value')->label('Total Value (GHS)')->numeric()->step(0.01)->prefix('GHS'),
            Textarea::make('description')->rows(2)->columnSpanFull(),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_date')->date()->sortable(),
                TextColumn::make('event_type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'birth', 'purchase', 'transfer_in' => 'success',
                        'death'                             => 'danger',
                        'sale', 'transfer_out'              => 'warning',
                        default                             => 'gray',
                    }),
                TextColumn::make('count')->label('Count'),
                TextColumn::make('source_or_destination')->label('From/To')->limit(25),
                TextColumn::make('total_value')->money('GHS')->label('Value'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('event_date', 'desc');
    }
}