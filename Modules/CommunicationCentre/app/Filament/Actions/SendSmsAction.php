<?php

namespace Modules\CommunicationCentre\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Modules\CommunicationCentre\Services\CommunicationService;

/**
 * Reusable Filament header action — SMS compose modal.
 *
 * Usage:
 *   SendSmsAction::make(toPhone: $this->record->phone, toName: $this->record->name)
 */
class SendSmsAction
{
    public static function make(
        string|\Closure $toPhone = '',
        string|\Closure $toName = '',
    ): Action {
        $resolvedPhone = is_callable($toPhone) ? $toPhone() : $toPhone;
        $resolvedName  = is_callable($toName) ? $toName() : $toName;

        return Action::make('sendSms')
            ->label('Send SMS')
            ->icon('heroicon-o-device-phone-mobile')
            ->color('warning')
            ->modalHeading('Compose SMS')
            ->modalWidth('md')
            ->form([
                TextInput::make('to_phone')
                    ->label('Recipient Phone')
                    ->tel()
                    ->required()
                    ->default($resolvedPhone)
                    ->helperText('Include country code, e.g. +233201234567'),
                TextInput::make('to_name')
                    ->label('Recipient Name')
                    ->default($resolvedName),
                Textarea::make('body')
                    ->label('Message')
                    ->required()
                    ->rows(4)
                    ->maxLength(640)
                    ->helperText('Max 640 characters (multi-part SMS).')
                    ->columnSpanFull(),
            ])
            ->action(function (array $data) {
                try {
                    /** @var CommunicationService $service */
                    $service = app(CommunicationService::class);

                    $service->sendRaw(
                        channel: 'sms',
                        toPhone: $data['to_phone'],
                        subject: null,
                        body:    $data['body'],
                    );

                    Notification::make()
                        ->success()
                        ->title('SMS Queued')
                        ->body('SMS to '.$data['to_phone'].' has been queued for delivery.')
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Failed to Send SMS')
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }
}
