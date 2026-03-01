<?php

namespace Modules\HR\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class RejectLeaveAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'reject_leave';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Reject Leave')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->visible(fn ($record) => $record->status === 'pending')
            ->form([
                Textarea::make('rejected_reason')
                    ->label('Rejection Reason')
                    ->required()
                    ->maxLength(500),
            ])
            ->action(function ($record, $data) {
                $this->process(function () use ($record, $data) {
                    $record->update([
                        'status' => 'rejected',
                        'rejected_reason' => $data['rejected_reason'],
                    ]);
                });

                Notification::make()
                    ->title('Leave Rejected')
                    ->body('The leave request has been rejected with the provided reason.')
                    ->warning()
                    ->send();
            });
    }
}
