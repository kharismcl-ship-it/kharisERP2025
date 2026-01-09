<?php

namespace Modules\Hostels\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommProviderConfig;
use Modules\Hostels\Models\HostelWhatsAppGroup;
use Modules\Hostels\Services\WhatsAppGroupService;
use Tests\TestCase;

class WhatsAppGroupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Wasender provider configuration for testing
        $config = CommProviderConfig::create([
            'name' => 'Wasender',
            'channel' => 'whatsapp',
            'provider' => 'wasender',
            'is_active' => true,
            'config' => [
                'base_url' => 'https://api.wasender.example.com',
                'api_key' => 'test-api-key',
                'device_id' => 'test-device-id',
            ],
        ]);

        // Debug: Check if config was created
        Log::debug('Provider config created:', ['id' => $config->id, 'config' => $config->toArray()]);
    }

    public function test_create_group()
    {
        Http::fake([
            'https://api.wasender.example.com/api/groups/create' => Http::response([
                'success' => true,
                'data' => ['id' => '12345'],
            ]),
        ]);

        $hostel = \Modules\Hostels\Models\Hostel::factory()->create();
        $group = HostelWhatsAppGroup::factory()->create(['hostel_id' => $hostel->id]);
        $tenant = \Modules\Hostels\Models\HostelOccupant::factory()->create(['hostel_id' => $hostel->id]);
        $group->occupants()->attach($tenant);

        $service = new WhatsAppGroupService;
        $result = $service->createGroup($group);

        $this->assertTrue($result);
        $this->assertDatabaseHas('hostel_whatsapp_groups', [
            'id' => $group->id,
            'group_id' => '12345',
        ]);
    }

    public function test_send_message()
    {
        Http::fake([
            'https://api.wasender.example.com/api/groups/send-message' => Http::response(['success' => true]),
        ]);

        $hostel = \Modules\Hostels\Models\Hostel::factory()->create();
        $group = HostelWhatsAppGroup::factory()->create(['hostel_id' => $hostel->id, 'group_id' => '12345']);

        $service = new WhatsAppGroupService;
        $result = $service->sendMessage($group, 'Test message');

        $this->assertTrue($result);
        $this->assertDatabaseHas('whatsapp_group_messages', [
            'whatsapp_group_id' => $group->id,
            'content' => 'Test message',
        ]);
    }

    public function test_add_participants()
    {
        Http::fake([
            'https://api.wasender.example.com/api/groups/add-participants' => Http::response(['success' => true]),
        ]);

        $hostel = \Modules\Hostels\Models\Hostel::factory()->create();
        $group = HostelWhatsAppGroup::factory()->create(['hostel_id' => $hostel->id, 'group_id' => '12345']);
        $tenant = \Modules\Hostels\Models\HostelOccupant::factory()->create(['hostel_id' => $hostel->id]);

        $service = new WhatsAppGroupService;
        $result = $service->addParticipants($group, [$tenant->id]);

        $this->assertTrue($result);
        $this->assertDatabaseHas('hostel_whatsapp_group_occupant', [
            'whatsapp_group_id' => $group->id,
            'hostel_occupant_id' => $tenant->id,
        ]);
    }
}
