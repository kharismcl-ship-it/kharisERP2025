<?php

namespace Modules\CommunicationCentre\Filament\Components;

use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Modules\CommunicationCentre\Models\CommPreference;

class NotificationPreferenceForm
{
    public static function make(string $notifiableType, array $defaultChannels = ['email', 'database'])
    {
        return Section::make('Notification Preferences')
            ->schema([
                CheckboxList::make('notification_channels')
                    ->label('Receive notifications via:')
                    ->options(function () {
                        $channels = config('communicationcentre.channels', []);
                        $channelLabels = [
                            'email' => 'Email',
                            'sms' => 'SMS',
                            'whatsapp' => 'WhatsApp',
                            'database' => 'In-App Notification',
                        ];

                        $options = [];
                        foreach ($channels as $channel) {
                            $options[$channel] = $channelLabels[$channel] ?? ucfirst($channel);
                        }

                        return $options;
                    })
                    ->default($defaultChannels)
                    ->columns(2)
                    ->afterStateHydrated(function ($component, $state, $record) use ($notifiableType) {
                        if ($record && $record->exists) {
                            $preferences = CommPreference::where('notifiable_type', $notifiableType)
                                ->where('notifiable_id', $record->id)
                                ->where('is_enabled', true)
                                ->pluck('channel')
                                ->toArray();

                            $component->state($preferences);
                        }
                    })
                    ->afterStateUpdated(function ($state, $record) use ($notifiableType) {
                        if ($record && $record->exists) {
                            // Get company ID from record - use a default company ID if not available
                            $companyId = $record->company_id ?? 1; // Default to company ID 1 if not set

                            // Delete existing preferences for this notifiable
                            CommPreference::where('notifiable_type', $notifiableType)
                                ->where('notifiable_id', $record->id)
                                ->delete();

                            // Create new preferences for selected channels
                            foreach ((array) $state as $channel) {
                                CommPreference::create([
                                    'company_id' => $companyId,
                                    'notifiable_type' => $notifiableType,
                                    'notifiable_id' => $record->id,
                                    'channel' => $channel,
                                    'is_enabled' => true,
                                ]);
                            }
                        }
                    })
                    ->dehydrated(false),
            ])
            ->collapsible();
    }
}
