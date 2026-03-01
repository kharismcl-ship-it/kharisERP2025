<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Modules\Finance\Services\RecurringInvoiceService;

class GenerateRecurringInvoicesCommand extends Command
{
    protected $signature = 'finance:generate-recurring-invoices';

    protected $description = 'Generate invoices for all due recurring invoice templates';

    public function handle(RecurringInvoiceService $service): int
    {
        $count = $service->generateDue();

        $this->info("Generated {$count} recurring invoice(s).");

        return self::SUCCESS;
    }
}
