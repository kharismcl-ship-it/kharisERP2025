<?php

namespace Modules\Hostels\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Hostels\Models\HostelOccupantUser;

class HostelOccupantUserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public HostelOccupantUser $hostelOccupantUser,
        public string $password
    ) {}
}
