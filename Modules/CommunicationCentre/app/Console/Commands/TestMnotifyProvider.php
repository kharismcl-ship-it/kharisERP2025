<?php

namespace Modules\CommunicationCentre\Console\Commands;

use Illuminate\Console\Command;
use Modules\CommunicationCentre\Models\CommMessage;
use Modules\CommunicationCentre\Models\CommProviderConfig;
use Modules\CommunicationCentre\Services\ChannelProviders\MnotifySmsProvider;

class TestMnotifyProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communication:test-mnotify {--to= : Recipient phone number} {--message= : Custom message text}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Mnotify SMS provider implementation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Mnotify SMS provider...');

        // Get or create a test provider config
        $providerConfig = CommProviderConfig::firstOrCreate(
            [
                'channel' => 'sms',
                'provider' => 'mnotify',
                'is_default' => true,
            ],
            [
                'name' => 'Test Mnotify Provider',
                'is_active' => true,
                'config' => [
                    'api_key' => 'your-mnotify-api-key-here',
                    'sender_id' => 'KHARIS',
                ],
            ]
        );

        // Create a test message
        $toPhone = $this->option('to') ?? '233242260004';
        $customMessage = $this->option('message') ?? 'This is a test SMS from Kharis ERP MnotifyProvider';

        $message = CommMessage::create([
            'company_id' => 1,
            'notifiable_type' => 'Modules\\CommunicationCentre\\Models\\CommMessage',
            'notifiable_id' => 1,
            'channel' => 'sms',
            'provider' => 'mnotify',
            'provider_config_id' => $providerConfig->id,
            'to_phone' => $toPhone,
            'subject' => 'Test SMS',
            'body' => $customMessage,
            'status' => 'queued',
        ]);

        // Test the provider
        $provider = new MnotifySmsProvider;

        try {
            $this->info("Sending SMS to: {$toPhone}");
            $this->info("Message: {$customMessage}");

            $provider->send($message);

            $this->info("Message status: {$message->status}");

            if ($message->status === 'sent') {
                $this->info("✅ SMS sent successfully! Message ID: {$message->provider_message_id}");
            } else {
                $this->error("❌ SMS failed: {$message->error_message}");
            }

        } catch (\Exception $e) {
            $this->error('Error sending SMS: '.$e->getMessage());
        }

        $this->info('Test completed.');
    }
}
