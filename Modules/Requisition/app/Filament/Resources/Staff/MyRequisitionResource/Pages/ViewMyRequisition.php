<?php

namespace Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource;
use Modules\Requisition\Models\RequisitionActivity;

class ViewMyRequisition extends ViewRecord
{
    protected static string $resource = MyRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Staff can only withdraw while still 'submitted' (not yet under review)
            Action::make('withdraw')
                ->label('Withdraw')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->outlined()
                ->visible(fn () => $this->record->status === 'submitted')
                ->form([
                    Textarea::make('cancellation_reason')
                        ->label('Reason for Withdrawal')
                        ->required()
                        ->rows(3),
                ])
                ->modalHeading('Withdraw Requisition')
                ->modalDescription('This will cancel your request. You may clone it later to start a new one.')
                ->action(function (array $data): void {
                    $oldStatus = $this->record->status;
                    $this->record->update([
                        'status'              => 'cancelled',
                        'cancellation_reason' => $data['cancellation_reason'],
                    ]);
                    RequisitionActivity::log(
                        $this->record,
                        'status_changed',
                        "Withdrawn by requester: {$data['cancellation_reason']}",
                        [],
                        $oldStatus,
                        'cancelled',
                    );
                    Notification::make()->success()->title('Requisition withdrawn.')->send();
                    $this->refreshFormData(['status', 'cancellation_reason']);
                }),

            // Staff can reorder (clone) resolved requisitions
            Action::make('reorder')
                ->label('Reorder')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->visible(fn () => in_array($this->record->status, ['approved', 'fulfilled', 'closed']))
                ->requiresConfirmation()
                ->modalHeading('Reorder / Clone Requisition')
                ->modalDescription('This will create a new draft requisition based on this one.')
                ->action(function (): void {
                    $original = $this->record;

                    $newReq = $original->replicate([
                        'reference',
                        'status',
                        'approved_by',
                        'approved_at',
                        'fulfilled_at',
                        'rejection_reason',
                        'cancellation_reason',
                    ]);
                    $newReq->status    = 'submitted'; // Staff go straight to submitted
                    $newReq->reference = null;
                    $newReq->save();

                    foreach ($original->items as $item) {
                        $newItem = $item->replicate(['requisition_id']);
                        $newItem->requisition_id = $newReq->id;
                        $newItem->save();
                    }

                    RequisitionActivity::log(
                        $newReq,
                        'requisition_created',
                        "Reordered from {$original->reference}.",
                    );

                    Notification::make()
                        ->success()
                        ->title("New requisition {$newReq->reference} submitted.")
                        ->send();

                    $this->redirect(
                        MyRequisitionResource::getUrl('index')
                    );
                }),
        ];
    }
}