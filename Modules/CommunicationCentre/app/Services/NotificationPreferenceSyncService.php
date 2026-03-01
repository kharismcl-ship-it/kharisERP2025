<?php

namespace Modules\CommunicationCentre\Services;

use Modules\CommunicationCentre\Models\CommPreference;

class NotificationPreferenceSyncService
{
    /**
     * Sync notification preferences for a notifiable entity
     */
    public function syncPreferences($notifiable, array $channels, ?int $companyId = null): void
    {
        $companyId = $companyId ?? $this->resolveCompanyId($notifiable);

        $currentPreferences = CommPreference::where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->where('company_id', $companyId)
            ->get();

        // Update existing preferences
        foreach ($currentPreferences as $preference) {
            $preference->update([
                'is_enabled' => in_array($preference->channel, $channels),
            ]);
        }

        // Create new preferences for channels that don't exist
        $existingChannels = $currentPreferences->pluck('channel')->toArray();
        $newChannels = array_diff($channels, $existingChannels);

        foreach ($newChannels as $channel) {
            CommPreference::create([
                'company_id' => $companyId,
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id,
                'channel' => $channel,
                'is_enabled' => true,
            ]);
        }

        // Disable channels that were removed
        $removedChannels = array_diff($existingChannels, $channels);
        foreach ($removedChannels as $channel) {
            CommPreference::where('notifiable_type', get_class($notifiable))
                ->where('notifiable_id', $notifiable->id)
                ->where('company_id', $companyId)
                ->where('channel', $channel)
                ->update(['is_enabled' => false]);
        }
    }

    /**
     * Get notification preferences for a notifiable entity
     */
    public function getPreferences($notifiable, ?int $companyId = null): array
    {
        $companyId = $companyId ?? $this->resolveCompanyId($notifiable);

        return CommPreference::where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->where('company_id', $companyId)
            ->where('is_enabled', true)
            ->pluck('channel')
            ->toArray();
    }

    /**
     * Check if a channel is enabled for a notifiable entity
     */
    public function isChannelEnabled($notifiable, string $channel, ?int $companyId = null): bool
    {
        $companyId = $companyId ?? $this->resolveCompanyId($notifiable);

        return CommPreference::where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->where('company_id', $companyId)
            ->where('channel', $channel)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * Bulk sync preferences for multiple entities
     */
    public function bulkSync(string $notifiableType, array $entities, array $channels, ?int $companyId = null): void
    {
        foreach ($entities as $entity) {
            $this->syncPreferences($entity, $channels, $companyId);
        }
    }

    /**
     * Import preferences from another entity
     */
    public function importPreferences($sourceNotifiable, $targetNotifiable, ?int $companyId = null): void
    {
        $companyId = $companyId ?? $this->resolveCompanyId($sourceNotifiable);

        $sourceChannels = $this->getPreferences($sourceNotifiable, $companyId);
        $this->syncPreferences($targetNotifiable, $sourceChannels, $companyId);
    }

    /**
     * Reset preferences to default channels
     */
    public function resetToDefault($notifiable, array $defaultChannels, ?int $companyId = null): void
    {
        $companyId = $companyId ?? $this->resolveCompanyId($notifiable);
        $this->syncPreferences($notifiable, $defaultChannels, $companyId);
    }

    /**
     * Resolve company ID from notifiable entity or session
     */
    protected function resolveCompanyId($notifiable): ?int
    {
        if (method_exists($notifiable, 'getCompanyId')) {
            return $notifiable->getCompanyId();
        }

        if (property_exists($notifiable, 'company_id')) {
            return $notifiable->company_id;
        }

        // Use session-based company ID resolution
        if (session()->has('current_company_id')) {
            return session('current_company_id');
        }

        return null;
    }

    /**
     * Get preference statistics for reporting
     */
    public function getStatistics(?int $companyId = null): array
    {
        $query = CommPreference::query();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return [
            'total_preferences' => $query->count(),
            'enabled_preferences' => $query->where('is_enabled', true)->count(),
            'disabled_preferences' => $query->where('is_enabled', false)->count(),
            'by_channel' => $query->where('is_enabled', true)
                ->groupBy('channel')
                ->selectRaw('channel, count(*) as count')
                ->pluck('count', 'channel')
                ->toArray(),
            'by_notifiable_type' => $query->where('is_enabled', true)
                ->groupBy('notifiable_type')
                ->selectRaw('notifiable_type, count(*) as count')
                ->pluck('count', 'notifiable_type')
                ->toArray(),
        ];
    }
}
