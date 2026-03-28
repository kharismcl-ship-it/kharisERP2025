<?php

namespace Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionActivity;

class RequisitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')->wrap()->limit(50),
                Tables\Columns\TextColumn::make('request_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Requisition::TYPES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('urgency')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        default  => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'approved'         => 'success',
                        'fulfilled'        => 'success',
                        'rejected'         => 'danger',
                        'cancelled'        => 'danger',
                        'submitted'        => 'info',
                        'under_review'     => 'warning',
                        'pending_revision' => 'warning',
                        default            => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => Requisition::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('targetDepartment.name')
                    ->label('Department')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('total_estimated_cost')
                    ->label('Est. Cost')
                    ->money('GHS'),
                Tables\Columns\TextColumn::make('created_at')->date()->label('Submitted'),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('reorder')
                    ->label('Reorder')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->visible(fn (Requisition $r) => in_array($r->status, ['approved', 'fulfilled', 'closed']))
                    ->requiresConfirmation()
                    ->modalHeading('Reorder / Clone Requisition')
                    ->modalDescription('This will create a new requisition based on this one.')
                    ->action(function (Requisition $record): void {
                        $newReq = $record->replicate([
                            'reference',
                            'status',
                            'approved_by',
                            'approved_at',
                            'fulfilled_at',
                            'rejection_reason',
                            'cancellation_reason',
                        ]);
                        $newReq->status    = 'submitted';
                        $newReq->reference = null;
                        $newReq->save();

                        foreach ($record->items as $item) {
                            $newItem = $item->replicate(['requisition_id']);
                            $newItem->requisition_id = $newReq->id;
                            $newItem->save();
                        }

                        RequisitionActivity::log(
                            $newReq,
                            'requisition_created',
                            "Reordered from {$record->reference}.",
                        );

                        Notification::make()
                            ->success()
                            ->title("New requisition {$newReq->reference} submitted.")
                            ->send();
                    }),

                DeleteAction::make()
                    ->visible(fn (Requisition $r) => in_array($r->status, ['draft', 'submitted', 'pending_revision'])),
            ]);
    }
}
