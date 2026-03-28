<?php

namespace Modules\ProcurementInventory\Filament\Resources\CycleCountResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Models\CycleCountLine;

class CycleCountLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Count Lines';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('counted_quantity')
                ->label('Counted Quantity')
                ->numeric()
                ->required(),
            Forms\Components\Textarea::make('notes')
                ->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.sku')
                    ->label('SKU')
                    ->searchable(),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable(),

                Tables\Columns\TextColumn::make('system_quantity')
                    ->label('System Qty')
                    ->numeric(4),

                Tables\Columns\TextColumn::make('counted_quantity')
                    ->label('Counted Qty')
                    ->numeric(4)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('variance')
                    ->label('Variance')
                    ->numeric(4)
                    ->placeholder('—')
                    ->color(fn ($state) => $state === null ? 'gray' : ((float) $state < 0 ? 'danger' : ((float) $state > 0 ? 'warning' : 'success'))),

                Tables\Columns\TextColumn::make('variance_pct')
                    ->label('Variance %')
                    ->numeric(2)
                    ->suffix('%')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'gray',
                        'counted'  => 'warning',
                        'approved' => 'info',
                        'adjusted' => 'success',
                        default    => 'gray',
                    }),
            ])
            ->headerActions([])
            ->actions([
                Action::make('enter_count')
                    ->label('Enter Count')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->visible(fn (CycleCountLine $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\TextInput::make('counted_quantity')
                            ->label('Counted Quantity')
                            ->numeric()
                            ->required(),
                        Forms\Components\Textarea::make('notes')->rows(2),
                    ])
                    ->action(function (CycleCountLine $record, array $data) {
                        $record->update([
                            'counted_quantity' => (float) $data['counted_quantity'],
                            'notes'            => $data['notes'] ?? $record->notes,
                        ]);
                        Notification::make()->title('Count recorded')->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }
}