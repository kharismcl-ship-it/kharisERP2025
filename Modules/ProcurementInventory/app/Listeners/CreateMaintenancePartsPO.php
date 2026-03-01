<?php

namespace Modules\ProcurementInventory\Listeners;

use Modules\Fleet\Events\MaintenancePartsRequested;

class CreateMaintenancePartsPO
{
    public function __construct(protected CreateDraftPurchaseOrder $creator) {}

    public function handle(MaintenancePartsRequested $event): void
    {
        $record    = $event->maintenanceRecord;
        $companyId = $record->company_id ?? null;

        if (! $companyId || empty($event->parts)) {
            return;
        }

        $this->creator->create(
            $companyId,
            "Auto-draft from Fleet maintenance — Vehicle #{$record->vehicle_id}. Record #{$record->id}. Please assign vendor and submit.",
            $event->parts,
            ['module_tag' => 'fleet']
        );
    }
}
