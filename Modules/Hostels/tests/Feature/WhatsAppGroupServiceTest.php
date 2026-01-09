<?php

namespace Modules\Hostels\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelWhatsAppGroup;
use Modules\Hostels\Models\Tenant;
use Modules\CommunicationCentre\Models\CommProviderConfig;
use Illuminate\Support\Facades\Http;
use Modules\Hostels\Services\WhatsAppGroupService;

class WhatsAppGroupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CommProviderConfig::create([
            'channel' => 'whatsapp',
            'provider' => 'wasender',
            'is_active' => true,
            'config' => [
                'base_url' => 'https://wasenderapi.com',
                'api_key' => 'test_api_key',
                'device_id' => 'test_device_id',
            ],
        ]);
    }

    public function test_create_group()
    {
        Http::fake([
            'https://wasenderapi.com/api/groups/create' => Http::response([
                'success' => true,
                'data' => ['id' => '12345'],
            ]),
        ]);

        $hostel = Hostel::factory()->create();
        $group = HostelWhatsAppGroup::factory()->create(['hostel_id' => $hostel->id]);
        $tenant = Tenant::factory()->create(['hostel_id' => $hostel->id]);
        $group->tenants()->attach($tenant);

        $service = new WhatsAppGroupService();
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
            'https://wasenderapi.com/api/groups/send-message' => Http::response(['success' => true]),
        ]);

        $hostel = Hostel::factory()->create();
        $group = HostelWhatsAppGroup::factory()->create(['hostel_id' => $hostel->id, 'group_id' => '12345']);

        $service = new WhatsAppGroupService();
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
            'https://wasenderapi.com/api/groups/add-participants' => Http::response(['success' => true]),
        ]);

        $hostel = Hostel::factory()->create();
        $group = HostelWhatsAppGroup::factory()->create(['hostel_id' => $hostel->id, 'group_id' => '12345']);
        $tenant = Tenant::factory()->create(['hostel_id' => $hostel->id]);

        $service = new WhatsAppGroupService();
        $result = $service->addParticipants($group, [$tenant->id]);

        $this->assertTrue($result);
        $this->assertDatabaseHas('hostel_whatsapp_group_tenant', [
            'hostel_whatsapp_group_id' => $group->id,
            'tenant_id' => $tenant->id,
        ]);
    }
}
