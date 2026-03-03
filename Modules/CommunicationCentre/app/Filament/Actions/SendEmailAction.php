<?php

namespace Modules\CommunicationCentre\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Modules\CommunicationCentre\Services\CommunicationService;

/**
 * Reusable Filament header action — email compose modal.
 *
 * Usage in any resource page's getHeaderActions():
 *   SendEmailAction::make(toEmail: $this->record->email, toName: $this->record->name)
 *
 * Or with closures resolved at call time:
 *   SendEmailAction::make(toEmail: fn() => $this->record->email)
 */
class SendEmailAction
{
    public static function make(
        string|\Closure $toEmail = '',
        string|\Closure $toName = '',
    ): Action {
        $resolvedEmail = is_callable($toEmail) ? $toEmail() : $toEmail;
        $resolvedName  = is_callable($toName) ? $toName() : $toName;

        return Action::make('sendEmail')
            ->label('Send Email')
            ->icon('heroicon-o-envelope')
            ->color('info')
            ->modalHeading('Compose Email')
            ->modalWidth('lg')
            ->form([
                TextInput::make('to_email')
                    ->label('Recipient Email')
                    ->email()
                    ->required()
                    ->default($resolvedEmail),
                TextInput::make('to_name')
                    ->label('Recipient Name')
                    ->default($resolvedName),
                TextInput::make('subject')
                    ->label('Subject')
                    ->required()
                    ->maxLength(255),
                Textarea::make('body')
                    ->label('Message Body')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data) {
                try {
                    /** @var CommunicationService $service */
                    $service = app(CommunicationService::class);

                    $service->sendRawEmail(
                        toEmail: $data['to_email'],
                        toName:  $data['to_name'] ?? null,
                        subject: $data['subject'],
                        body:    $data['body'],
                    );

                    Notification::make()
                        ->success()
                        ->title('Email Queued')
                        ->body('Email to '.$data['to_email'].' has been queued for delivery.')
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Failed to Send Email')
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }
}
