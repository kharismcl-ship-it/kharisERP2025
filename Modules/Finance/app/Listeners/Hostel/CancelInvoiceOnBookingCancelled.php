<?php

namespace Modules\Finance\Listeners\Hostel;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\Hostels\Events\BookingCancelled;

class CancelInvoiceOnBookingCancelled
{
    public function handle(BookingCancelled $event): void
    {
        $booking = $event->booking;
        $refundAmount = $event->refundAmount;

        $invoice = Invoice::where('hostel_id', $booking->hostel_id)
            ->where(function ($q) use ($booking) {
                $q->where('customer_id', $booking->hostel_occupant_id)
                    ->orWhere('customer_type', 'hostel_occupant');
            })
            ->whereNotIn('status', ['cancelled'])
            ->latest()
            ->first();

        if (! $invoice) {
            Log::info('CancelInvoiceOnBookingCancelled: no active invoice found', [
                'booking_id' => $booking->id,
            ]);

            return;
        }

        $invoice->update(['status' => 'cancelled']);

        Log::info('CancelInvoiceOnBookingCancelled: invoice cancelled', [
            'invoice_id'     => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'booking_id'     => $booking->id,
        ]);

        // If a refund was issued, post a credit note journal entry
        if ($refundAmount > 0) {
            $this->postCreditNoteJournal($invoice, $refundAmount);
        }
    }

    private function postCreditNoteJournal(Invoice $invoice, float $refundAmount): void
    {
        $companyId = $invoice->company_id;

        $entry = JournalEntry::create([
            'company_id'  => $companyId,
            'date'        => now(),
            'reference'   => 'CN-'.$invoice->invoice_number,
            'description' => 'Credit note — booking cancellation refund for invoice '.$invoice->invoice_number,
        ]);

        // DR Revenue (reverse income), CR Accounts Receivable (reduce AR)
        // Using account codes from standard COA seeder
        $revenueAccount = \Modules\Finance\Models\Account::where('code', '4110')
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhereNull('company_id');
            })
            ->first();

        $arAccount = \Modules\Finance\Models\Account::where('code', '1200')
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhereNull('company_id');
            })
            ->first();

        if ($revenueAccount) {
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $revenueAccount->id,
                'debit'            => $refundAmount,
                'credit'           => 0,
            ]);
        }

        if ($arAccount) {
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $arAccount->id,
                'debit'            => 0,
                'credit'           => $refundAmount,
            ]);
        }

        Log::info('CancelInvoiceOnBookingCancelled: credit note journal posted', [
            'journal_entry_id' => $entry->id,
            'refund_amount'    => $refundAmount,
            'invoice_id'       => $invoice->id,
        ]);
    }
}
