<?php

namespace Modules\CommunicationCentre\Traits;

use Modules\CommunicationCentre\Models\CommPreference;

trait HasNotificationPreferences
{
    public function notificationPreferences()
    {
        return $this->morphMany(CommPreference::class, 'notifiable');
    }

    public function getNotificationChannelsAttribute()
    {
        return $this->notificationPreferences()
            ->where('is_enabled', true)
            ->pluck('channel')
            ->toArray();
    }

    public function setNotificationChannels(array $channels)
    {
        // Remove existing preferences
        $this->notificationPreferences()->delete();

        // Create new preferences
        foreach ($channels as $channel) {
            CommPreference::create([
                'notifiable_type' => static::class,
                'notifiable_id' => $this->id,
                'channel' => $channel,
                'is_enabled' => true,
                'company_id' => $this->company_id ?? null,
            ]);
        }
    }
}
