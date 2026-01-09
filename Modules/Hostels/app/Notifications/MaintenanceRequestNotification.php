<?php

namespace Modules\Hostels\Notifications;

use Modules\Hostels\Models\MaintenanceRequest;

class MaintenanceRequestNotification
{
    /**
     * Get the template code for this notification
     */
    public static function getTemplateCode(): string
    {
        return 'maintenance_request';
    }

    /**
     * Get the display name for this notification template
     */
    public static function getTemplateName(): string
    {
        return 'Maintenance Request';
    }

    /**
     * Get the placeholders used in this notification
     */
    public static function getPlaceholders(): array
    {
        return [
            // Add placeholders as needed for maintenance requests
        ];
    }

    /**
     * Send maintenance request notification to staff
     *
     * @return void
     */
    public function send(MaintenanceRequest $request)
    {
        // In a real implementation, you would send this to maintenance staff
        // For now, we'll just log that it would be sent
        \Log::info('Maintenance request notification would be sent for request ID: '.$request->id);
    }
}
