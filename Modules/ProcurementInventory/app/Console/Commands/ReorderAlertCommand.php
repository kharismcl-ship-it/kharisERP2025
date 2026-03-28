<?php

namespace Modules\ProcurementInventory\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\ProcurementInventory\Models\StockLevel;

class ReorderAlertCommand extends Command
{
    protected $signature = 'procurement:reorder-alert {--company= : Limit to a specific company ID}';

    protected $description = 'Send low-stock alerts for items that have fallen below their reorder level';

    public function handle(): int
    {
        $query = StockLevel::with(['item', 'company'])
            ->whereHas('item', fn ($q) => $q->where('reorder_level', '>', 0)->where('is_active', true));

        if ($companyId = $this->option('company')) {
            $query->where('company_id', $companyId);
        }

        $levels = $query->get()->filter(fn (StockLevel $sl) => $sl->needsReorder());

        if ($levels->isEmpty()) {
            $this->info('No items below reorder level.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($levels as $stockLevel) {
            try {
                $this->sendAlert($stockLevel);
                $this->createDraftRequisition($stockLevel);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("ReorderAlert failed for StockLevel #{$stockLevel->id}: " . $e->getMessage());
                $this->warn("Failed for {$stockLevel->item->sku}: " . $e->getMessage());
            }
        }

        $this->info("Reorder alerts sent for {$sent} item(s).");

        return self::SUCCESS;
    }

    private function createDraftRequisition(StockLevel $stockLevel): void
    {
        if (! class_exists(\Modules\Requisition\Models\Requisition::class)) {
            return;
        }

        $item = $stockLevel->item;

        // Only create if no open draft reorder requisition already exists for this item
        $exists = \Modules\Requisition\Models\Requisition::where('company_id', $stockLevel->company_id)
            ->where('title', "Reorder: {$item->name}")
            ->where('status', 'draft')
            ->exists();

        if ($exists) {
            return;
        }

        \Modules\Requisition\Models\Requisition::create([
            'company_id'  => $stockLevel->company_id,
            'request_type'=> 'material',
            'title'       => "Reorder: {$item->name}",
            'description' => "Auto-generated reorder — stock at {$stockLevel->quantity_on_hand} {$item->unit_of_measure} (reorder level: {$item->reorder_level})",
            'urgency'     => 'medium',
            'status'      => 'draft',
        ]);
    }

    private function sendAlert(StockLevel $stockLevel): void
    {
        if (! class_exists(\Modules\CommunicationCentre\Models\CommTemplate::class)) {
            return;
        }

        $template = \Modules\CommunicationCentre\Models\CommTemplate::where('code', 'stock_low_alert')->first();
        if (! $template) {
            return;
        }

        $item = $stockLevel->item;

        // Resolve recipient — company admin users or a configured address
        $recipients = \App\Models\User::whereHas('companies', fn ($q) => $q->where('companies.id', $stockLevel->company_id))
            ->where('is_active', true)
            ->take(5)
            ->pluck('email')
            ->filter()
            ->toArray();

        if (empty($recipients)) {
            return;
        }

        $variables = [
            'item_name'       => $item->name,
            'sku'             => $item->sku,
            'quantity_on_hand' => number_format((float) $stockLevel->quantity_on_hand, 2),
            'reorder_level'   => number_format((float) $item->reorder_level, 2),
            'reorder_quantity' => number_format((float) ($item->reorder_quantity ?? 0), 2),
            'unit_of_measure' => $item->unit_of_measure ?? 'units',
            'recipient_name'  => 'Procurement Manager',
        ];

        $service = app(\Modules\CommunicationCentre\Services\CommunicationService::class);

        foreach ($recipients as $email) {
            $variables['recipient_name'] = $email;
            $service->sendFromTemplate(
                $template,
                ['email' => $email],
                $variables
            );
        }
    }
}
