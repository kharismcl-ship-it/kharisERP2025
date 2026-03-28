<?php

declare(strict_types=1);

namespace Modules\Requisition\Listeners;

use Modules\Requisition\Events\RequisitionStatusChanged;
use Modules\Requisition\Services\RequisitionWorkflowService;

class ApplyWorkflowRulesOnSubmission
{
    public function __construct(
        private readonly RequisitionWorkflowService $workflowService,
    ) {}

    public function handle(RequisitionStatusChanged $event): void
    {
        if ($event->requisition->status !== 'submitted') {
            return;
        }

        $this->workflowService->applyWorkflowRules($event->requisition);
    }
}