<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Modules\Finance\Services\IntegrationService;
use Modules\PaymentsChannel\Models\PayTransaction;

class SyncPaymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync-payments {--limit=100 : Number of transactions to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync payment transactions to finance payments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(IntegrationService $integrationService)
    {
        $limit = $this->option('limit');

        $transactions = PayTransaction::where('status', 'successful')
            ->whereHas('payIntent', function ($query) {
                $query->whereNotNull('metadata->invoice_id');
            })
            ->limit($limit)
            ->get();

        $this->info("Processing {$transactions->count()} transactions...");

        $bar = $this->output->createProgressBar($transactions->count());
        $bar->start();

        foreach ($transactions as $transaction) {
            try {
                $payment = $integrationService->processPaymentTransaction($transaction);
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("Failed to process transaction ID {$transaction->id}: ".$e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('Payment synchronization completed!');

        return 0;
    }
}
