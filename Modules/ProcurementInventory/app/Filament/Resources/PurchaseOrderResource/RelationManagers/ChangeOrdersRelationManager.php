<?php

namespace Modules\ProcurementInventory\Filament\Resources\PurchaseOrderResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Models\PoChangeOrder;

class ChangeOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'changeOrders';

    protected static ?string $title = 'Change Orders';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('change_order_number')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('change_type')
                    ->badge(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(60),

                Tables\Columns\TextColumn::make('amount_change')
                    ->money('GHS')
                    ->color(fn (PoChangeOrder $record): string => (float) $record->amount_change > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'            => 'gray',
                        'pending_approval' => 'warning',
                        'approved'         => 'success',
                        'rejected'         => 'danger',
                        default            => 'gray',
                    }),
            ])
            ->headerActions([])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PoChangeOrder $record) => $record->status === 'pending_approval')
                    ->requiresConfirmation()
                    ->action(function (PoChangeOrder $record) {
                        $record->update([
                            'status'              => 'approved',
                            'approved_by_user_id' => auth()->id(),
                            'approved_at'         => now(),
                        ]);
                        Notification::make()->title('Change order approved')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (PoChangeOrder $record) => $record->status === 'pending_approval')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (PoChangeOrder $record, array $data) {
                        $record->update([
                            'status'           => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()->title('Change order rejected')->warning()->send();
                    }),
            ])
            ->bulkActions([]);
    }
}