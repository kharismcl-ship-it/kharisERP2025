<?php

namespace Modules\Finance\Listeners\Finance;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Finance\Events\InvoiceMarkedOverdue;

class SendInvoiceOverdueNotification
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(InvoiceMarkedOverdue $event): void
    {
        $invoice = $event->invoice;

        $daysOverdue = (int) now()->diffInDays($invoice->due_date, false) * -1;

        $data = [
            'invoice_number' => $invoice->invoice_number,
            'customer_name'  => $invoice->customer_name ?? 'Valued Customer',
            'due_date'       => $invoice->due_date?->format('d M Y'),
            'days_overdue'   => $daysOverdue,
            'total'          => number_format((float) $invoice->total, 2),
            'currency'       => 'GHS',
        ];

        try {
            $this->comms->sendToModel(
                $invoice,
                'email',
                'finance_invoice_overdue',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendInvoiceOverdueNotification failed', [
                'invoice_id' => $invoice->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}