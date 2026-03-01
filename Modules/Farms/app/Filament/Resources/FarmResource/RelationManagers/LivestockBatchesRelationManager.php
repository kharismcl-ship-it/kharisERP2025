<?php

namespace Modules\Farms\Filament\Resources\FarmResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Farms\Models\LivestockBatch;

class LivestockBatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'livestockBatches';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('animal_type')
                ->options(array_combine(LivestockBatch::ANIMAL_TYPES, array_map('ucfirst', LivestockBatch::ANIMAL_TYPES)))
                ->required(),
            TextInput::make('breed')->maxLength(255),
            DatePicker::make('acquisition_date')->required(),
            TextInput::make('initial_count')->required()->numeric()->minValue(1),
            TextInput::make('current_count')->required()->numeric()->minValue(0),
            TextInput::make('acquisition_cost')->label('Acquisition Cost')->numeric()->prefix('GHS')->step(0.01),
            Select::make('status')
                ->options(array_combine(LivestockBatch::STATUSES, array_map('ucfirst', LivestockBatch::STATUSES)))
                ->default('active'),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_reference')->label('Ref'),
                TextColumn::make('animal_type')->label('Type')->badge(),
                TextColumn::make('breed'),
                TextColumn::make('acquisition_date')->date()->sortable(),
                TextColumn::make('initial_count')->label('Initial'),
                TextColumn::make('current_count')->label('Current'),
                TextColumn::make('acquisition_cost')->money('GHS')->label('Cost'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'      => 'success',
                        'sold'        => 'info',
                        'slaughtered' => 'warning',
                        'deceased'    => 'danger',
                        default       => 'gray',
                    }),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('acquisition_date', 'desc');
    }
}
