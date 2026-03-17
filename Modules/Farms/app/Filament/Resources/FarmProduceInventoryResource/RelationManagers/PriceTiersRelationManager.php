<?php

namespace Modules\Farms\Filament\Resources\FarmProduceInventoryResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriceTiersRelationManager extends RelationManager
{
    protected static string $relationship = 'priceTiers';

    protected static ?string $title = 'Bulk / Tier Pricing';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('min_quantity')
                ->label('Min Qty (applies when order ≥ this)')
                ->numeric()
                ->step(0.001)
                ->required(),
            TextInput::make('price_per_unit')
                ->label('Price per Unit (GHS)')
                ->numeric()
                ->step(0.01)
                ->prefix('GHS')
                ->required(),
            TextInput::make('label')
                ->label('Label (e.g. "Wholesale", "Bulk 10+")')
                ->maxLength(100)
                ->placeholder('Optional display label'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('min_quantity')
                    ->label('Min Qty')
                    ->numeric(3),
                TextColumn::make('price_per_unit')
                    ->label('Price / Unit')
                    ->money('GHS'),
                TextColumn::make('label')
                    ->placeholder('—'),
            ])
            ->defaultSort('min_quantity')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['company_id'] = $this->getOwnerRecord()->company_id;
                        return $data;
                    }),
            ])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
