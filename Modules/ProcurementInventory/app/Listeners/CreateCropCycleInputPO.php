<?php

namespace Modules\ProcurementInventory\Listeners;

use Modules\Farms\Events\CropCycleStarted;

class CreateCropCycleInputPO
{
    public function __construct(protected CreateDraftPurchaseOrder $creator) {}

    public function handle(CropCycleStarted $event): void
    {
        $cycle     = $event->cropCycle;
        $companyId = $cycle->company_id ?? null;

        if (! $companyId || empty($event->requiredInputs)) {
            return;
        }

        $this->creator->create(
            $companyId,
            "Auto-draft from Farms — Crop cycle #{$cycle->id} ({$cycle->name ?? 'new cycle'}) started. Please assign vendor and submit.",
            $event->requiredInputs,
            ['farm_id' => $cycle->farm_id ?? null, 'module_tag' => 'farms']
        );
    }
}
