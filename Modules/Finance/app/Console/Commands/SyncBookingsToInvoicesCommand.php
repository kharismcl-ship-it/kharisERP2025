<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\IntegrationService;
use Modules\Hostels\Models\Booking;

class SyncBookingsToInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync-bookings {--limit=100 : Number of bookings to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync hostel bookings to finance invoices';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(IntegrationService $integrationService)
    {
        $limit = $this->option('limit');

        $bookings = Booking::whereDoesntHave('charges')
            ->where('status', 'confirmed')
            ->limit($limit)
            ->get();

        $this->info("Processing {$bookings->count()} bookings...");

        $bar = $this->output->createProgressBar($bookings->count());
        $bar->start();

        foreach ($bookings as $booking) {
            try {
                $invoice = $integrationService->createInvoiceForBooking($booking);

                // Link the invoice to the booking for future reference
                $booking->charges()->create([
                    'description' => 'Booking invoice: '.$invoice->invoice_number,
                    'amount' => $invoice->total,
                    'type' => 'invoice',
                    'reference_id' => $invoice->id,
                ]);

                $bar->advance();
            } catch (\Exception $e) {
                $this->error("Failed to process booking ID {$booking->id}: ".$e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('Booking synchronization completed!');

        return 0;
    }
}
