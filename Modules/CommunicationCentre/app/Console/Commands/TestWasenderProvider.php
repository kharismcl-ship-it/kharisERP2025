<?php

namespace Modules\CommunicationCentre\Console\Commands;

use Illuminate\Console\Command;
use Modules\CommunicationCentre\Models\CommMessage;
use Modules\CommunicationCentre\Models\CommProviderConfig;
use Modules\CommunicationCentre\Services\ChannelProviders\WasenderProvider;

class TestWasenderProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communication:test-wasender {--type=text : Message type (text, image, video, document)} {--to= : Recipient phone number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Wasender provider implementation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Wasender provider...');

        // Get or create a test provider config
        $providerConfig = CommProviderConfig::firstOrCreate(
            [
                'channel' => 'whatsapp',
                'provider' => 'wasender',
                'is_default' => true,
            ],
            [
                'name' => 'Test Wasender Provider',
                'is_active' => true,
                'config' => [
                    'base_url' => 'https://www.wasenderapi.com',
                    'token' => 'test_token',
                    'device_id' => 'test_device',
                ],
            ]
        );

        // Create a test message
        $messageType = $this->option('type');
        $toPhone = $this->option('to') ?? '+1234567890';

        $messageData = [
            'notifiable_type' => 'Modules\CommunicationCentre\Models\CommMessage',
            'notifiable_id' => 1,
            'channel' => 'whatsapp',
            'provider' => 'wasender',
            'to_phone' => $toPhone,
            'status' => 'queued',
        ];

        switch ($messageType) {
            case 'image':
                $messageData['body'] = 'https://example.com/image.jpg';
                $messageData['subject'] = 'Test Image';
                break;

            case 'video':
                $messageData['body'] = 'https://example.com/video.mp4';
                $messageData['subject'] = 'Test Video';
                break;

            case 'document':
                $messageData['body'] = 'https://example.com/document.pdf';
                $messageData['subject'] = 'Test Document';
                break;

            case 'text':
            default:
                $messageData['body'] = 'This is a test message from WasenderProvider';
                break;
        }

        $message = CommMessage::create($messageData);

        // Test the provider
        $provider = new WasenderProvider;

        try {
            $provider->send($message);
            $this->info("Message sent successfully! Status: {$message->status}");
        } catch (\Exception $e) {
            $this->error('Error sending message: '.$e->getMessage());
        }

        $this->info('Test completed.');
    }
}
