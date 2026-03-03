<?php

namespace Modules\Farms\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Farms\Events\CropCycleStarted;

class NotifyCropCycleStarted
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(CropCycleStarted $event): void
    {
        $cycle   = $event->cropCycle;
        $company = $cycle->company ?? null;

        if (! $company?->email) {
            return;
        }

        $data = [
            'farm_name'              => $cycle->farm?->name ?? 'Farm',
            'crop_name'              => $cycle->crop_name,
            'variety'                => $cycle->variety ?? '',
            'planting_date'          => $cycle->planting_date?->format('d M Y') ?? 'Unknown',
            'expected_harvest_date'  => $cycle->expected_harvest_date?->format('d M Y') ?? 'TBD',
            'planted_area'           => number_format((float) $cycle->planted_area, 2) . ' ' . $cycle->planted_area_unit,
        ];

        try {
            $this->comms->sendToContact(
                'email',
                $company->email,
                null,
                null,
                'farms_crop_cycle_started',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyCropCycleStarted failed', [
                'crop_cycle_id' => $cycle->id,
                'error'         => $e->getMessage(),
            ]);
        }
    }
}
