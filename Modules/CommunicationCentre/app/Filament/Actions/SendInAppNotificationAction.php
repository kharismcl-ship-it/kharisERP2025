<?php

namespace Modules\CommunicationCentre\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Modules\CommunicationCentre\Models\CommMessage;
use Modules\CommunicationCentre\Services\ChannelProviders\FilamentDatabaseProvider;

/**
 * Reusable Filament header action — in-app (database) notification modal.
 *
 * Sends a Filament database notification to a selected user or a pre-resolved user.
 *
 * Usage:
 *   SendInAppNotificationAction::make(toUserId: $this->record->user_id)
 */
class SendInAppNotificationAction
{
    public static function make(int|\Closure|null $toUserId = null): Action
    {
        $resolvedUserId = is_callable($toUserId) ? $toUserId() : $toUserId;

        return Action::make('sendInApp')
            ->label('Send Notification')
            ->icon('heroicon-o-bell')
            ->color('gray')
            ->modalHeading('Send In-App Notification')
            ->modalWidth('md')
            ->form(function () use ($resolvedUserId) {
                $userModel = config('auth.providers.users.model', \App\Models\User::class);

                $fields = [
                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('body')
                        ->label('Message')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                ];

                if ($resolvedUserId === null) {
                    array_unshift($fields, Select::make('user_id')
                        ->label('Recipient User')
                        ->options(fn () => $userModel::query()->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required());
                }

                return $fields;
            })
            ->action(function (array $data) use ($resolvedUserId) {
                try {
                    $userId = $resolvedUserId ?? ($data['user_id'] ?? null);

                    if (! $userId) {
                        throw new \Exception('No recipient user selected.');
                    }

                    $userModel = config('auth.providers.users.model', \App\Models\User::class);
                    $user = $userModel::find($userId);

                    if (! $user) {
                        throw new \Exception('Recipient user not found.');
                    }

                    $companyId = auth()->user()?->current_company_id;

                    $message = CommMessage::create([
                        'company_id'      => $companyId,
                        'notifiable_type' => get_class($user),
                        'notifiable_id'   => $user->id,
                        'channel'         => 'database',
                        'provider'        => 'filament_database',
                        'to_name'         => $user->name ?? '',
                        'to_email'        => $user->email ?? null,
                        'subject'         => $data['title'],
                        'body'            => $data['body'],
                        'status'          => 'queued',
                    ]);

                    $provider = app(FilamentDatabaseProvider::class);
                    $provider->send($message);

                    Notification::make()
                        ->success()
                        ->title('Notification Sent')
                        ->body('In-app notification delivered to '.$user->name.'.')
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Failed to Send Notification')
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }
}
