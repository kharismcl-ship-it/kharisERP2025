<?php

namespace Modules\Finance\Services;

use Modules\Finance\Models\Account;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\Finance\Models\Payment;
use Modules\Hostels\Models\Booking;
use Modules\PaymentsChannel\Models\PayTransaction;

class IntegrationService
{
    /**
     * Create an invoice for a hostel booking
     *
     * @return Invoice
     */
    public function createInvoiceForBooking(Booking $booking)
    {
        // Create the invoice
        $invoice = Invoice::create([
            'company_id' => $booking->hostel->company_id ?? null,
            'customer_name' => $booking->hostelOccupant->full_name ?? 'Unknown Hostel Occupant',
            'customer_type' => 'hostel_occupant',
            'customer_id' => $booking->hostel_occupant_id,
            'invoice_number' => 'INV-'.date('Y').'-'.strtoupper(uniqid()),
            'invoice_date' => now(),
            'due_date' => $booking->check_in_date,
            'status' => 'pending',
            'sub_total' => $booking->total_amount,
            'tax_total' => 0,
            'total' => $booking->total_amount,
            'hostel_id' => $booking->hostel_id,
        ]);

        // Create an invoice line for the booking
        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => 'Room booking: '.($booking->room->name ?? 'Room'),
            'quantity' => 1,
            'unit_price' => $booking->total_amount,
            'line_total' => $booking->total_amount,
        ]);

        // Create journal entries for accounting
        $journalEntry = JournalEntry::create([
            'company_id' => $invoice->company_id,
            'date' => now(),
            'reference' => 'Invoice: '.$invoice->invoice_number,
            'description' => 'Booking invoice created for hostel occupant: '.$invoice->customer_name,
        ]);

        // Debit accounts receivable
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('accounts_receivable', $invoice->company_id),
            'description' => 'Accounts Receivable for Booking Invoice',
            'debit' => $invoice->total,
            'credit' => 0,
        ]);

        // Credit revenue
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('revenue', $invoice->company_id),
            'description' => 'Revenue from Booking',
            'debit' => 0,
            'credit' => $invoice->total,
        ]);

        return $invoice;
    }

    /**
     * Process a payment received through payment channels
     *
     * @return Payment|null
     */
    public function processPaymentTransaction(PayTransaction $transaction)
    {
        $intent = $transaction->payIntent;
        $metadata = $intent?->metadata ?? [];
        if (empty($metadata['invoice_id'])) {
            return null;
        }

        $invoice = Invoice::find($metadata['invoice_id']);

        if (! $invoice) {
            return null;
        }

        // Create a payment record
        $payment = Payment::create([
            'company_id' => $invoice->company_id,
            'invoice_id' => $invoice->id,
            'amount' => $transaction->amount,
            'payment_date' => $transaction->processed_at ?? $transaction->created_at,
            'payment_method' => $intent?->payMethod?->code ?? $transaction->provider,
            'reference' => $transaction->provider_transaction_id,
        ]);

        // Update invoice status based on amount paid
        $totalPaid = $invoice->payments()->sum('amount');

        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partial']);
        }

        // Create journal entries for the payment
        $journalEntry = JournalEntry::create([
            'company_id' => $payment->company_id,
            'date' => $payment->payment_date,
            'reference' => 'Payment: '.$payment->reference,
            'description' => 'Payment received for invoice: '.$invoice->invoice_number,
        ]);

        // Debit bank/cash account
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('bank', $payment->company_id),
            'description' => 'Bank deposit from payment',
            'debit' => $payment->amount,
            'credit' => 0,
        ]);

        // Credit accounts receivable
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('accounts_receivable', $payment->company_id),
            'description' => 'Reduction in Accounts Receivable',
            'debit' => 0,
            'credit' => $payment->amount,
        ]);

        return $payment;
    }

    /**
     * Get account ID by account type/name
     * This is a placeholder that would need to be implemented based on actual chart of accounts
     *
     * @return int
     */
    private function getAccountId(string $accountType, ?int $companyId = null)
    {
        $map = [
            'accounts_receivable' => ['code' => 'AR', 'type' => 'asset', 'name' => 'Accounts Receivable'],
            'revenue' => ['code' => 'REV', 'type' => 'income', 'name' => 'Revenue'],
            'bank' => ['code' => 'BANK', 'type' => 'asset', 'name' => 'Bank Account'],
        ];

        $hint = $map[$accountType] ?? null;
        if (! $hint) {
            // Create a default account if none exists
            $account = Account::where('company_id', $companyId)->first();
            if (! $account && $companyId) {
                $account = Account::create([
                    'company_id' => $companyId,
                    'code' => 'CASH',
                    'name' => 'Cash Account',
                ]);
            }

            return $account->id ?? null;
        }

        $query = Account::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        // Prefer exact code match
        $account = $query->where('code', $hint['code'])->first();
        if ($account) {
            return $account->id;
        }

        // Fallback by type
        $query = Account::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        $account = $query->where('type', $hint['type'])->first();
        if ($account) {
            return $account->id;
        }

        // Fallback by name pattern
        $query = Account::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        $account = $query->where('name', 'like', '%'.$hint['name'].'%')->first();
        if ($account) {
            return $account->id;
        }

        // Create default account if none found
        if ($companyId) {
            $account = Account::create([
                'company_id' => $companyId,
                'code' => $hint['code'],
                'name' => $hint['name'],
            ]);

            return $account->id;
        }

        return null;
    }

    /**
     * Find invoice by module and entity
     *
     * @param  mixed  $entityId
     * @return Invoice|null
     */
    public function findInvoiceByModuleEntity(string $module, string $entityType, $entityId)
    {
        return Invoice::where('module', $module)
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->first();
    }

    /**
     * Create invoice with module/entity tracking
     *
     * @return Invoice
     */
    public function createInvoice(array $data)
    {
        return Invoice::create(array_merge([
            'invoice_number' => 'INV-'.date('Y').'-'.strtoupper(uniqid()),
            'invoice_date' => now(),
            'status' => 'pending',
            'tax_total' => 0,
        ], $data));
    }

    /**
     * Process payment completion using unified event data
     *
     * @return Payment|null
     */
    public function processPaymentFromEvent(array $paymentData)
    {
        $invoice = $this->findInvoiceByModuleEntity(
            $paymentData['module'],
            $paymentData['entity_type'],
            $paymentData['entity_id']
        );

        if (! $invoice) {
            return null;
        }

        $payment = Payment::create([
            'company_id' => $invoice->company_id,
            'invoice_id' => $invoice->id,
            'amount' => $paymentData['amount'],
            'payment_date' => now(),
            'payment_method' => $paymentData['payment_method'],
            'reference' => $paymentData['transaction_id'] ?? uniqid(),
        ]);

        // Update invoice status
        $totalPaid = $invoice->payments()->sum('amount');

        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partial']);
        }

        return $payment;
    }

    /**
     * Record payment for an invoice with detailed payment data
     *
     * @return Payment|null
     */
    public function recordPayment(int $invoiceId, array $paymentData)
    {
        $invoice = Invoice::find($invoiceId);

        if (! $invoice) {
            return null;
        }

        $payment = Payment::create([
            'company_id' => $invoice->company_id,
            'invoice_id' => $invoice->id,
            'amount' => $paymentData['amount'],
            'payment_date' => now(),
            'payment_method' => $paymentData['payment_method'],
            'reference' => $paymentData['reference'] ?? uniqid(),
            'metadata' => $paymentData['metadata'] ?? [],
        ]);

        // Update invoice status
        $totalPaid = $invoice->payments()->sum('amount');

        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partial']);
        }

        return $payment;
    }
}
