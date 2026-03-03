<?php

namespace Modules\CommunicationCentre\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Modules\CommunicationCentre\Services\CommunicationService;

/**
 * Reusable Filament header action — WhatsApp message modal.
 *
 * Usage:
 *   SendWhatsAppAction::make(toPhone: $this->record->phone, toName: $this->record->name)
 */
class SendWhatsAppAction
{
    public static function make(
        string|\Closure $toPhone = '',
        string|\Closure $toName = '',
    ): Action {
        $resolvedPhone = is_callable($toPhone) ? $toPhone() : $toPhone;
        $resolvedName  = is_callable($toName) ? $toName() : $toName;

        return Action::make('sendWhatsApp')
            ->label('Send WhatsApp')
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->color('success')
            ->modalHeading('Send WhatsApp Message')
            ->modalWidth('md')
            ->form([
                TextInput::make('to_phone')
                    ->label('Recipient Phone (WhatsApp)')
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
                    ->rows(5)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data) {
                try {
                    /** @var CommunicationService $service */
                    $service = app(CommunicationService::class);

                    $service->sendRaw(
                        channel: 'whatsapp',
                        toPhone: $data['to_phone'],
                        subject: null,
                        body:    $data['body'],
                    );

                    Notification::make()
                        ->success()
                        ->title('WhatsApp Message Queued')
                        ->body('Message to '.$data['to_phone'].' has been queued for delivery.')
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Failed to Send WhatsApp')
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }
}
