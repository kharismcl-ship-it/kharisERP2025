<?php

namespace Modules\Finance\Tests\Feature;

use Modules\Finance\Services\IntegrationService;
use Tests\TestCase;

class IntegrationServiceTest extends TestCase
{
    /**
     * Test that the IntegrationService can be instantiated.
     *
     * @return void
     */
    public function test_integration_service_can_be_instantiated()
    {
        $service = app(IntegrationService::class);
        $this->assertInstanceOf(IntegrationService::class, $service);
    }

    /**
     * Test that console commands are registered.
     *
     * @return void
     */
    public function test_console_commands_are_registered()
    {
        $this->artisan('finance:sync-bookings --help')
            ->assertExitCode(0);

        $this->artisan('finance:sync-payments --help')
            ->assertExitCode(0);
    }
}
