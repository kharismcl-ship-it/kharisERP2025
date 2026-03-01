<?php

namespace Modules\Farms\Filament\Resources\FarmResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables;
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
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('acquisition_date', 'desc');
    }
}
