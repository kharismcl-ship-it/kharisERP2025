<?php

namespace Modules\ProcurementInventory\Listeners;

use Modules\Construction\Events\ProjectPhaseApproved;

class CreateProjectMaterialsPO
{
    public function __construct(protected CreateDraftPurchaseOrder $creator) {}

    public function handle(ProjectPhaseApproved $event): void
    {
        $phase     = $event->projectPhase;
        $companyId = $phase->company_id ?? ($phase->constructionProject->company_id ?? null);

        if (! $companyId || empty($event->materials)) {
            return;
        }

        $this->creator->create(
            $companyId,
            "Auto-draft from Construction — Phase '{$phase->name}' approved (Project #{$phase->construction_project_id}). Please assign vendor and submit.",
            $event->materials,
            ['project_id' => $phase->construction_project_id ?? null, 'module_tag' => 'construction']
        );
    }
}
