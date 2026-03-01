<?php

namespace Modules\CommunicationCentre\Filament\Components;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Modules\CommunicationCentre\Models\CommPreference;

class NotificationPreferenceDisplay
{
    public static function make(string $notifiableType, string $notifiableClass)
    {
        return Section::make('Notification Preferences')
            ->description('Preferred notification channels')
            ->collapsible()
            ->schema([
                TextEntry::make('_notification_channels')
                    ->label('Enabled Channels')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(function ($record) use ($notifiableType) {
                        $preferences = CommPreference::where('notifiable_type', $notifiableType)
                            ->where('notifiable_id', $record->id)
                            ->where('is_enabled', true)
                            ->distinct()
                            ->pluck('channel')
                            ->toArray();

                        if (empty($preferences)) {
                            return 'All channels enabled';
                        }

                        $channelLabels = [
                            'email' => 'Email',
                            'sms' => 'SMS',
                            'whatsapp' => 'WhatsApp',
                            'database' => 'In-App',
                        ];

                        $enabledChannels = array_map(function ($channel) use ($channelLabels) {
                            return $channelLabels[$channel] ?? ucfirst($channel);
                        }, $preferences);

                        // Remove any duplicates to ensure each channel appears only once
                        $enabledChannels = array_unique($enabledChannels);

                        return implode(', ', $enabledChannels);
                    })
                    ->columnSpanFull(),
            ]);
    }
}
