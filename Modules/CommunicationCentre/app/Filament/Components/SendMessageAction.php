<?php

namespace Modules\CommunicationCentre\Filament\Components;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Modules\CommunicationCentre\Models\CommTemplate;
use Modules\CommunicationCentre\Services\CommunicationService;

class SendMessageAction
{
    public static function make(string $recipientName, string $recipientContact, string $defaultChannel = 'email'): Action
    {
        return Action::make('sendMessage')
            ->label('Send Message')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->form([
                Forms\Components\Select::make('channel')
                    ->label('Channel')
                    ->options(function () {
                        $channels = config('communicationcentre.channels', []);
                        $channelLabels = [
                            'email' => 'Email',
                            'sms' => 'SMS',
                            'whatsapp' => 'WhatsApp',
                            'database' => 'Database Notification',
                        ];

                        $options = [];
                        foreach ($channels as $channel) {
                            $options[$channel] = $channelLabels[$channel] ?? ucfirst($channel);
                        }

                        return $options;
                    })
                    ->default($defaultChannel)
                    ->required()
                    ->live(),

                Forms\Components\Select::make('template_id')
                    ->label('Use Template')
                    ->options(function (Get $get) {
                        $channel = $get('channel');

                        if (! $channel) {
                            return [];
                        }

                        return CommTemplate::where('channel', $channel)
                            ->where('is_active', true)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state) {
                            $template = CommTemplate::find($state);
                            if ($template) {
                                $set('subject', $template->subject);
                                $set('message', $template->content);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('subject')
                    ->label('Subject')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('recipient_name')->default($recipientName),
                Forms\Components\Hidden::make('recipient_contact')->default($recipientContact),
            ])
            ->action(function (array $data) {
                try {
                    $communicationService = app(CommunicationService::class);

                    // Determine if contact is email or phone
                    $isEmail = filter_var($data['recipient_contact'], FILTER_VALIDATE_EMAIL);

                    $message = $communicationService->sendRaw(
                        channel: $data['channel'],
                        toPhone: $isEmail ? null : $data['recipient_contact'],
                        subject: $data['subject'],
                        body: $data['message']
                    );

                    // Professional approach: Message is queued, not immediately delivered
                    Notification::make()
                        ->success()
                        ->title('Message Queued')
                        ->body('Message has been queued for delivery to '.$data['recipient_name'].
                               '. Delivery status will be updated when processed.')
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Error')
                        ->body('Failed to send message: '.$e->getMessage())
                        ->send();
                }
            });
    }
}
