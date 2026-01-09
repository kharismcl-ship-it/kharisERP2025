<?php

namespace Modules\CommunicationCentre\Traits;

use Modules\CommunicationCentre\Models\CommMessage;
use Modules\CommunicationCentre\Models\CommPreference;

trait HasCommunicationProfile
{
    /**
     * Get all communication messages for this model.
     */
    public function commMessages()
    {
        return $this->morphMany(CommMessage::class, 'notifiable');
    }

    /**
     * Get communication preferences for this model.
     */
    public function commPreferences()
    {
        return $this->morphMany(CommPreference::class, 'notifiable');
    }

    /**
     * Get the name for communication purposes.
     */
    public function getCommName()
    {
        return $this->name ?? $this->full_name ?? null;
    }

    /**
     * Get the email for communication purposes.
     */
    public function getCommEmail()
    {
        return $this->email ?? null;
    }

    /**
     * Get the phone number for communication purposes.
     */
    public function getCommPhone()
    {
        return $this->phone ?? $this->phone_no ?? $this->mobile ?? null;
    }

    /**
     * Check if communication is enabled for a specific channel.
     */
    public function isCommEnabled($channel)
    {
        $preference = $this->commPreferences()
            ->where('channel', $channel)
            ->first();

        return $preference ? $preference->is_enabled : true;
    }
}
