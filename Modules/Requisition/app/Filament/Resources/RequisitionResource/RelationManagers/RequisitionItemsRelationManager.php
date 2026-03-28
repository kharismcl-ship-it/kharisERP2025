<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Modules\Requisition\Models\RequisitionItemCostAllocation;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RequisitionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Item Details')->schema([
                Grid::make(2)->schema([
                    Select::make('item_id')
                        ->label('Catalog Item (optional)')
                        ->relationship('procurementItem', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    TextInput::make('description')->required()->maxLength(255),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('quantity')->numeric()->default(1)->required(),
                    TextInput::make('unit')->default('pcs')->required(),
                    TextInput::make('unit_cost')->label('Unit Cost (GHS)')->numeric()->nullable(),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),

            Section::make('Vendor Quote')->schema([
                Grid::make(3)->schema([
                    TextInput::make('vendor_name')->label('Vendor Name')->nullable()->maxLength(255),
                    TextInput::make('vendor_quote_ref')->label('Quote Reference')->nullable()->maxLength(255),
                    TextInput::make('vendor_unit_price')->label('Vendor Unit Price (GHS)')->numeric()->nullable(),
                ]),
            ]),

            Section::make('Fulfilment Tracking')->schema([
                TextInput::make('fulfilled_quantity')
                    ->label('Fulfilled Quantity')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Track how much of this item has been received/fulfilled.'),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')->searchable()->limit(40),
                TextColumn::make('procurementItem.name')->label('Catalog Item')->placeholder('—'),
                TextColumn::make('quantity'),
                TextColumn::make('fulfilled_quantity')
                    ->label('Fulfilled')
                    ->formatStateUsing(fn ($state, $record) => $record->fulfilled_quantity . ' / ' . $record->quantity . ' (' . $record->fulfilmentPercentage() . '%)')
                    ->color(fn ($record) => $record->isFullyFulfilled() ? 'success' : 'gray'),
                TextColumn::make('unit'),
                TextColumn::make('unit_cost')->money('GHS')->placeholder('—'),
                TextColumn::make('total_cost')->money('GHS')->placeholder('—'),
                TextColumn::make('vendor_name')->label('Vendor')->placeholder('—')->toggleable(),
                TextColumn::make('vendor_unit_price')->label('Vendor Price')->money('GHS')->placeholder('—')->toggleable(),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([
                Action::make('cost_split')
                    ->label('Cost Split')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('info')
                    ->modalHeading('Multi Cost Centre Allocation')
                    ->modalDescription('Allocate this item\'s cost across multiple cost centres. Percentages must sum to 100.')
                    ->form([
                        Repeater::make('allocations')
                            ->label('Cost Centre Allocations')
                            ->schema([
                                Select::make('cost_centre_id')
                                    ->label('Cost Centre')
                                    ->relationship('costCentre', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('percentage')
                                    ->label('Percentage (%)')
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->maxValue(100)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Cost Centre')
                            ->minItems(1),
                    ])
                    ->fillForm(fn ($record) => [
                        'allocations' => $record->costAllocations->map(fn ($a) => [
                            'cost_centre_id' => $a->cost_centre_id,
                            'percentage'     => $a->percentage,
                        ])->toArray(),
                    ])
                    ->action(function ($record, array $data) {
                        $total = array_sum(array_column($data['allocations'], 'percentage'));
                        if (abs($total - 100) > 0.01) {
                            Notification::make()
                                ->danger()
                                ->title("Percentages must sum to 100. Current total: {$total}%")
                                ->send();
                            return;
                        }
                        // Replace all allocations
                        $record->costAllocations()->delete();
                        foreach ($data['allocations'] as $alloc) {
                            RequisitionItemCostAllocation::create([
                                'requisition_item_id' => $record->id,
                                'cost_centre_id'      => $alloc['cost_centre_id'],
                                'percentage'          => $alloc['percentage'],
                                'amount'              => round((float) ($record->total_cost ?? 0) * (float) $alloc['percentage'] / 100, 2),
                            ]);
                        }
                        Notification::make()->success()->title('Cost allocation saved.')->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}