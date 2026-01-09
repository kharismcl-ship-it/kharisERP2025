<?php

namespace Modules\CommunicationCentre\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class CommTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default communication templates
        $templates = [
            // Email templates
            [
                'code' => 'welcome_email',
                'channel' => 'email',
                'name' => 'Welcome Email',
                'subject' => 'Welcome to our service, {{name}}!',
                'body' => "Dear {{name}},\n\nWelcome to our service! We're excited to have you on board.\n\nBest regards,\nThe Team",
                'description' => 'Welcome email for new users',
                'is_active' => true,
            ],
            [
                'code' => 'booking_confirmation',
                'channel' => 'email',
                'name' => 'Booking Confirmation',
                'subject' => 'Booking Confirmation #{{booking_reference}}',
                'body' => "Dear {{name}},\n\nYour booking (Reference: {{booking_reference}}) has been confirmed.\n\nCheck-in Date: {{check_in_date}}\nCheck-out Date: {{check_out_date}}\n\nThank you for choosing our service.\n\nBest regards,\nThe Team",
                'description' => 'Booking confirmation email for hostel tenants',
                'is_active' => true,
            ],
            [
                'code' => 'payment_receipt',
                'channel' => 'email',
                'name' => 'Payment Receipt',
                'subject' => 'Payment Receipt for Booking #{{booking_reference}}',
                'body' => "Dear {{name}},\n\nWe have received your payment of {{amount}} for booking #{{booking_reference}}.\n\nTransaction Date: {{date}}\n\nThank you for your payment.\n\nBest regards,\nThe Team",
                'description' => 'Payment receipt email',
                'is_active' => true,
            ],

            // SMS templates
            [
                'code' => 'booking_confirmation',
                'channel' => 'sms',
                'name' => 'Booking Confirmation SMS',
                'subject' => null,
                'body' => 'Your booking (Ref: {{booking_reference}}) is confirmed. Check-in: {{check_in_date}}. Thank you!',
                'description' => 'Booking confirmation SMS for hostel tenants',
                'is_active' => true,
            ],
            [
                'code' => 'check_in_notification',
                'channel' => 'sms',
                'name' => 'Check-in Notification',
                'subject' => null,
                'body' => 'Welcome {{name}}! You have been checked in to room {{room_number}}. Enjoy your stay!',
                'description' => 'Check-in notification SMS',
                'is_active' => true,
            ],
            [
                'code' => 'checkout_reminder',
                'channel' => 'sms',
                'name' => 'Checkout Reminder',
                'subject' => null,
                'body' => 'Reminder: Your checkout date is {{checkout_date}} for room {{room_number}}. Please prepare to check out.',
                'description' => 'Checkout reminder SMS',
                'is_active' => true,
            ],

            // WhatsApp templates
            [
                'code' => 'maintenance_request',
                'channel' => 'whatsapp',
                'name' => 'Maintenance Request Notification',
                'subject' => null,
                'body' => "New maintenance request #{{request_id}} has been submitted.\n\nIssue: {{issue_description}}\nRoom: {{room_number}}\nSubmitted by: {{tenant_name}}",
                'description' => 'Maintenance request notification for staff',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $templateData) {
            CommTemplate::firstOrCreate(
                [
                    'code' => $templateData['code'],
                    'channel' => $templateData['channel'],
                ],
                $templateData
            );
        }
    }
}
