<?php

namespace Modules\Hostels\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;

class HostelOccupantOtpRequested
{
    use SerializesModels;

    public HostelOccupant $hostelOccupant;

    public string $code;

    public ?Hostel $hostel;

    public function __construct(HostelOccupant $hostelOccupant, string $code, ?Hostel $hostel = null)
    {
        $this->hostelOccupant = $hostelOccupant;
        $this->code = $code;
        $this->hostel = $hostel;
    }
}
