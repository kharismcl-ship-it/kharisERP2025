<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Requisition\Filament\Resources\RequisitionResource;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionActivity;

class ViewRequisition extends ViewRecord
{
    protected static string $resource = RequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submit')
                ->label('Submit')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->visible(fn () => $this->record->status === 'draft')
                ->requiresConfirmation()
                ->modalHeading('Submit Requisition')
                ->modalDescription('Once submitted, this request will be available for review and approval.')
                ->action(function (): void {
                    $this->record->update(['status' => 'submitted']);
                    Notification::make()->info()->title('Requisition submitted for review.')->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => in_array($this->record->status, ['submitted', 'under_review']))
                ->requiresConfirmation()
                ->modalHeading('Approve Requisition')
                ->action(function (): void {
                    $this->record->update([
                        'status'      => 'approved',
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ]);
                    Notification::make()->success()->title('Requisition approved.')->send();
                    $this->refreshFormData(['status', 'approved_at', 'approved_by']);
                }),

            Action::make('return_for_revision')
                ->label('Return for Revision')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->visible(fn () => in_array($this->record->status, ['submitted', 'under_review', 'approved']))
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Revision Notes')
                        ->helperText('Explain what needs to be changed before re-submission.')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status'           => 'pending_revision',
                        'rejection_reason' => $data['rejection_reason'],
                    ]);
                    RequisitionActivity::log(
                        $this->record,
                        'revision_requested',
                        "Returned for revision: {$data['rejection_reason']}",
                    );
                    Notification::make()->warning()->title('Returned for revision.')->send();
                    $this->refreshFormData(['status', 'rejection_reason']);
                }),

            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => in_array($this->record->status, ['submitted', 'under_review', 'approved']))
                ->form([
                    Textarea::make('rejection_reason')->label('Rejection Reason')->required()->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status'           => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                    ]);
                    Notification::make()->danger()->title('Requisition rejected.')->send();
                    $this->refreshFormData(['status', 'rejection_reason']);
                }),

            Action::make('mark_fulfilled')
                ->label('Mark Fulfilled')
                ->icon('heroicon-o-archive-box')
                ->color('success')
                ->visible(fn () => $this->record->status === 'approved')
                ->requiresConfirmation()
                ->modalHeading('Mark as Fulfilled')
                ->modalDescription('Confirm that this requisition has been fully fulfilled.')
                ->action(function (): void {
                    $this->record->update([
                        'status'       => 'fulfilled',
                        'fulfilled_at' => now(),
                    ]);
                    Notification::make()->success()->title('Requisition marked as fulfilled.')->send();
                    $this->refreshFormData(['status', 'fulfilled_at']);
                }),

            Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-lock-closed')
                ->color('gray')
                ->visible(fn () => $this->record->status === 'fulfilled')
                ->requiresConfirmation()
                ->modalHeading('Close Requisition')
                ->modalDescription('Closing archives this requisition. This is a final action.')
                ->action(function (): void {
                    $this->record->update(['status' => 'closed']);
                    Notification::make()->success()->title('Requisition closed and archived.')->send();
                    $this->refreshFormData(['status']);
                }),

            EditAction::make(),
        ];
    }
}