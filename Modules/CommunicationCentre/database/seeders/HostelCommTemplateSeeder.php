<?php

namespace Modules\CommunicationCentre\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class HostelCommTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create hostel-specific communication templates
        $templates = [
            // Hostel booking confirmation email
            [
                'code' => 'hostel_booking_confirmation',
                'channel' => 'email',
                'name' => 'Hostel Booking Confirmation',
                'subject' => 'Booking Confirmation for {{hostel_name}} - Room {{room_number}}',
                'body' => "Dear {{tenant_name}},\n\nYour booking at {{hostel_name}} has been confirmed.\n\nBooking Reference: {{booking_reference}}\nRoom Number: {{room_number}}\nBed Number: {{bed_number}}\nCheck-in Date: {{check_in_date}}\nCheck-out Date: {{check_out_date}}\nTotal Amount: {{total_amount}}\n\nPlease present this email and a valid ID at check-in.\n\nBest regards,\nThe {{hostel_name}} Team",
                'description' => 'Hostel booking confirmation email',
                'is_active' => true,
            ],

            // Hostel check-in notification SMS
            [
                'code' => 'hostel_check_in_notification',
                'channel' => 'sms',
                'name' => 'Hostel Check-in Notification',
                'subject' => null,
                'body' => 'Welcome {{tenant_name}}! You have been checked in to {{hostel_name}}, Room {{room_number}}, Bed {{bed_number}}. Enjoy your stay!',
                'description' => 'Hostel check-in notification SMS',
                'is_active' => true,
            ],

            // Hostel payment receipt
            [
                'code' => 'hostel_payment_receipt',
                'channel' => 'email',
                'name' => 'Hostel Payment Receipt',
                'subject' => 'Payment Receipt for Booking #{{booking_reference}}',
                'body' => "Dear {{tenant_name}},\n\nWe confirm receipt of your payment of {{amount}} for booking #{{booking_reference}} at {{hostel_name}}.\n\nTransaction Date: {{date}}\n\nThank you for your payment.\n\nBest regards,\nThe {{hostel_name}} Team",
                'description' => 'Hostel payment receipt email',
                'is_active' => true,
            ],

            // Hostel checkout reminder
            [
                'code' => 'hostel_checkout_reminder',
                'channel' => 'sms',
                'name' => 'Hostel Checkout Reminder',
                'subject' => null,
                'body' => 'Reminder: Your checkout date is {{checkout_date}} from {{hostel_name}}, Room {{room_number}}. Please prepare to check out.',
                'description' => 'Hostel checkout reminder SMS',
                'is_active' => true,
            ],

            // Hostel maintenance request
            [
                'code' => 'hostel_maintenance_request',
                'channel' => 'whatsapp',
                'name' => 'Hostel Maintenance Request',
                'subject' => null,
                'body' => "New maintenance request #{{request_id}}\n\nIssue: {{issue_description}}\nHostel: {{hostel_name}}\nRoom: {{room_number}}\nSubmitted by: {{tenant_name}}\nDate: {{date}}",
                'description' => 'Hostel maintenance request notification',
                'is_active' => true,
            ],

            // Hostel incident report
            [
                'code' => 'hostel_incident_report',
                'channel' => 'email',
                'name' => 'Hostel Incident Report',
                'subject' => 'Incident Report #{{incident_id}} at {{hostel_name}}',
                'body' => "Incident Report #{{incident_id}}\n\nDate: {{date}}\nHostel: {{hostel_name}}\nRoom: {{room_number}}\nDescription: {{description}}\nReported by: {{reported_by}}\n\nPlease take necessary actions.\n\nBest regards,\nHostel Management",
                'description' => 'Hostel incident report notification',
                'is_active' => true,
            ],

            // Pre-arrival welcome email
            [
                'code' => 'hostel_pre_arrival_welcome',
                'channel' => 'email',
                'name' => 'Hostel Pre-Arrival Welcome',
                'subject' => 'Welcome to {{hostel_name}} - Important Pre-Arrival Information',
                'body' => "Dear {{tenant_name}},\n\nWelcome to {{hostel_name}}! We're excited to have you stay with us.\n\nYour check-in date: {{check_in_date}}\nYour room: {{room_number}}\nYour bed: {{bed_number}}\n\n**Important Information:**\n- Check-in time: {{check_in_time}}\n- Address: {{hostel_address}}\n- What to bring: Valid ID, booking confirmation\n- Contact: {{hostel_phone}} / {{hostel_email}}\n- Hostel rules: Please review our policies attached\n\n**Getting Here:**\n{{directions}}\n\nWe look forward to welcoming you!\n\nBest regards,\nThe {{hostel_name}} Team",
                'description' => 'Pre-arrival welcome email with important information',
                'is_active' => true,
            ],

            // Pre-arrival reminder (3 days before)
            [
                'code' => 'hostel_pre_arrival_reminder',
                'channel' => 'sms',
                'name' => 'Hostel Pre-Arrival Reminder',
                'subject' => null,
                'body' => 'Reminder: Your check-in at {{hostel_name}} is in 3 days ({{check_in_date}}). Room {{room_number}}, Bed {{bed_number}}. Contact: {{hostel_phone}}',
                'description' => 'Pre-arrival SMS reminder 3 days before check-in',
                'is_active' => true,
            ],

            // Pre-arrival final reminder (1 day before)
            [
                'code' => 'hostel_pre_arrival_final',
                'channel' => 'sms',
                'name' => 'Hostel Pre-Arrival Final Reminder',
                'subject' => null,
                'body' => 'Final reminder: Check-in tomorrow at {{hostel_name}}! Room {{room_number}}. Check-in time: {{check_in_time}}. Bring valid ID. Safe travels!',
                'description' => 'Final pre-arrival SMS reminder 1 day before check-in',
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
