<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Invoice;

class MarkOverdueInvoicesCommand extends Command
{
    protected $signature = 'finance:mark-overdue';

    protected $description = 'Mark invoices as overdue where due_date has passed and status is still sent/draft';

    public function handle(): int
    {
        $updated = Invoice::query()
            ->whereIn('status', ['draft', 'sent'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->update(['status' => 'overdue']);

        Log::info("finance:mark-overdue — {$updated} invoice(s) marked overdue");

        $this->info("Marked {$updated} invoice(s) as overdue.");

        return self::SUCCESS;
    }
}
