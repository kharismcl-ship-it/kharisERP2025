<?php

namespace Modules\ProcurementInventory\Filament\Resources\ProcurementContractResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Models\Item;

class ContractLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Contract Lines';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('item_id')
                ->label('Item')
                ->options(function () {
                    $companyId = $this->ownerRecord->company_id ?? auth()->user()?->current_company_id;
                    return Item::query()
                        ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                        ->where('is_active', true)
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->nullable()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $item = Item::find($state);
                        if ($item) {
                            $set('description', $item->name);
                            $set('unit_of_measure', $item->unit_of_measure);
                        }
                    }
                }),

            Forms\Components\TextInput::make('description')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('unit_of_measure')
                ->label('UOM')
                ->placeholder('pcs, kg…'),

            Forms\Components\TextInput::make('agreed_unit_price')
                ->label('Agreed Unit Price')
                ->numeric()
                ->required()
                ->prefix('GHS')
                ->step(0.0001),

            Forms\Components\TextInput::make('min_quantity')
                ->label('Min Qty')
                ->numeric()
                ->nullable()
                ->step(0.0001),

            Forms\Components\TextInput::make('max_quantity')
                ->label('Max Qty')
                ->numeric()
                ->nullable()
                ->step(0.0001),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.name')->label('Item')->placeholder('—'),
                Tables\Columns\TextColumn::make('description')->limit(40),
                Tables\Columns\TextColumn::make('unit_of_measure')->label('UOM'),
                Tables\Columns\TextColumn::make('agreed_unit_price')->money('GHS')->label('Unit Price'),
                Tables\Columns\TextColumn::make('min_quantity')->label('Min Qty')->numeric(4)->placeholder('—'),
                Tables\Columns\TextColumn::make('max_quantity')->label('Max Qty')->numeric(4)->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}