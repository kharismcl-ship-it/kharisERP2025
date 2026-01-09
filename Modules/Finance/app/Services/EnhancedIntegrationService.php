<?php

namespace Modules\Finance\Services;

use Modules\Finance\Models\Account;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Payment;
use Modules\Hostels\Models\Booking;
use Modules\PaymentsChannel\Models\PayTransaction;

class EnhancedIntegrationService
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
            'company_id' => $booking->hostelOccupant->company_id ?? null,
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
        \Modules\Finance\Models\InvoiceLine::create([
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
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('accounts_receivable', $invoice->company_id),
            'description' => 'Accounts Receivable for Booking Invoice',
            'debit' => $invoice->total,
            'credit' => 0,
        ]);

        // Credit revenue
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('revenue', $invoice->company_id),
            'description' => 'Revenue from Booking',
            'debit' => 0,
            'credit' => $invoice->total,
        ]);

        return $invoice;
    }

    /**
     * Create an invoice for a farm sale
     *
     * @param  mixed  $farmSale
     * @return Invoice
     */
    public function createInvoiceForFarmSale($farmSale)
    {
        // Create the invoice
        $invoice = Invoice::create([
            'company_id' => $farmSale->company_id,
            'customer_name' => $farmSale->customer_name,
            'customer_type' => 'farm_customer',
            'invoice_number' => 'INV-'.date('Y').'-'.strtoupper(uniqid()),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'pending',
            'sub_total' => $farmSale->total_amount,
            'tax_total' => 0,
            'total' => $farmSale->total_amount,
            'farm_id' => $farmSale->farm_id,
        ]);

        // Create an invoice line for the farm sale
        \Modules\Finance\Models\InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => 'Farm product: '.$farmSale->product_type,
            'quantity' => $farmSale->quantity,
            'unit_price' => $farmSale->unit_price,
            'line_total' => $farmSale->total_amount,
        ]);

        // Create journal entries for accounting
        $journalEntry = JournalEntry::create([
            'company_id' => $invoice->company_id,
            'date' => now(),
            'reference' => 'Invoice: '.$invoice->invoice_number,
            'description' => 'Farm sale invoice created for customer: '.$invoice->customer_name,
        ]);

        // Debit accounts receivable
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('accounts_receivable', $invoice->company_id),
            'description' => 'Accounts Receivable for Farm Sale',
            'debit' => $invoice->total,
            'credit' => 0,
        ]);

        // Credit revenue
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('revenue', $invoice->company_id),
            'description' => 'Revenue from Farm Sale',
            'debit' => 0,
            'credit' => $invoice->total,
        ]);

        return $invoice;
    }

    /**
     * Create an invoice for a construction project
     *
     * @param  mixed  $project
     * @return Invoice
     */
    public function createInvoiceForConstructionProject($project, array $lineItems)
    {
        $subTotal = collect($lineItems)->sum('total');

        // Create the invoice
        $invoice = Invoice::create([
            'company_id' => $project->company_id,
            'customer_name' => $project->client_name,
            'customer_type' => 'construction_client',
            'invoice_number' => 'INV-'.date('Y').'-'.strtoupper(uniqid()),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'pending',
            'sub_total' => $subTotal,
            'tax_total' => 0,
            'total' => $subTotal,
            'construction_project_id' => $project->id,
        ]);

        // Create invoice lines
        foreach ($lineItems as $item) {
            \Modules\Finance\Models\InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['total'],
            ]);
        }

        // Create journal entries for accounting
        $journalEntry = JournalEntry::create([
            'company_id' => $invoice->company_id,
            'date' => now(),
            'reference' => 'Invoice: '.$invoice->invoice_number,
            'description' => 'Construction project invoice for client: '.$invoice->customer_name,
        ]);

        // Debit accounts receivable
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('accounts_receivable', $invoice->company_id),
            'description' => 'Accounts Receivable for Construction Project',
            'debit' => $invoice->total,
            'credit' => 0,
        ]);

        // Credit revenue
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('revenue', $invoice->company_id),
            'description' => 'Revenue from Construction Project',
            'debit' => 0,
            'credit' => $invoice->total,
        ]);

        return $invoice;
    }

    /**
     * Create an invoice for a manufacturing batch
     *
     * @param  mixed  $batch
     * @return Invoice
     */
    public function createInvoiceForManufacturingBatch($batch)
    {
        // Create the invoice
        $invoice = Invoice::create([
            'company_id' => $batch->company_id,
            'customer_name' => 'Batch Customer', // Would be determined by actual customer
            'customer_type' => 'manufacturing_customer',
            'invoice_number' => 'INV-'.date('Y').'-'.strtoupper(uniqid()),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'pending',
            'sub_total' => 0, // Would be calculated based on batch value
            'tax_total' => 0,
            'total' => 0, // Would be calculated based on batch value
            'plant_id' => $batch->plant_id,
        ]);

        // Create an invoice line for the manufacturing batch
        \Modules\Finance\Models\InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => 'Manufactured product batch: '.$batch->batch_number,
            'quantity' => 1, // Would depend on the product type
            'unit_price' => 0, // Would be calculated based on batch value
            'line_total' => 0, // Would be calculated based on batch value
        ]);

        // Create journal entries for accounting
        $journalEntry = JournalEntry::create([
            'company_id' => $invoice->company_id,
            'date' => now(),
            'reference' => 'Invoice: '.$invoice->invoice_number,
            'description' => 'Manufacturing batch invoice',
        ]);

        // Debit accounts receivable
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('accounts_receivable', $invoice->company_id),
            'description' => 'Accounts Receivable for Manufacturing Batch',
            'debit' => $invoice->total,
            'credit' => 0,
        ]);

        // Credit revenue
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('revenue', $invoice->company_id),
            'description' => 'Revenue from Manufacturing Batch',
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
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('bank', $payment->company_id),
            'description' => 'Bank deposit from payment',
            'debit' => $payment->amount,
            'credit' => 0,
        ]);

        // Credit accounts receivable
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('accounts_receivable', $payment->company_id),
            'description' => 'Reduction in Accounts Receivable',
            'debit' => 0,
            'credit' => $payment->amount,
        ]);

        return $payment;
    }

    /**
     * Record an expense for procurement
     *
     * @param  mixed  $purchaseOrder
     * @return void
     */
    public function recordProcurementExpense($purchaseOrder)
    {
        // Create journal entries for the procurement expense
        $journalEntry = JournalEntry::create([
            'company_id' => $purchaseOrder->company_id,
            'date' => now(),
            'reference' => 'PO: '.$purchaseOrder->po_number,
            'description' => 'Procurement expense for Purchase Order: '.$purchaseOrder->po_number,
        ]);

        // Debit expense account
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('procurement_expense'),
            'description' => 'Procurement Expense',
            'debit' => $purchaseOrder->total_amount,
            'credit' => 0,
        ]);

        // Credit accounts payable
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('accounts_payable'),
            'description' => 'Accounts Payable for Purchase Order',
            'debit' => 0,
            'credit' => $purchaseOrder->total_amount,
        ]);
    }

    /**
     * Record fuel expense for fleet
     *
     * @param  mixed  $fuelLog
     * @return void
     */
    public function recordFleetFuelExpense($fuelLog)
    {
        // Create journal entries for the fuel expense
        $journalEntry = JournalEntry::create([
            'company_id' => $fuelLog->company_id,
            'date' => $fuelLog->date,
            'reference' => 'Fuel Log: '.$fuelLog->id,
            'description' => 'Fleet fuel expense',
        ]);

        // Debit fuel expense account
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('fuel_expense'),
            'description' => 'Fleet Fuel Expense',
            'debit' => $fuelLog->cost,
            'credit' => 0,
        ]);

        // Credit bank/cash account
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('bank'),
            'description' => 'Payment for Fuel',
            'debit' => 0,
            'credit' => $fuelLog->cost,
        ]);
    }

    /**
     * Record maintenance expense for fleet
     *
     * @param  mixed  $maintenanceRecord
     * @return void
     */
    public function recordFleetMaintenanceExpense($maintenanceRecord)
    {
        // Create journal entries for the maintenance expense
        $journalEntry = JournalEntry::create([
            'company_id' => $maintenanceRecord->company_id,
            'date' => $maintenanceRecord->date,
            'reference' => 'Maintenance: '.$maintenanceRecord->id,
            'description' => 'Fleet maintenance expense',
        ]);

        // Debit maintenance expense account
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('maintenance_expense'),
            'description' => 'Fleet Maintenance Expense',
            'debit' => $maintenanceRecord->cost,
            'credit' => 0,
        ]);

        // Credit bank/cash account
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('bank'),
            'description' => 'Payment for Maintenance',
            'debit' => 0,
            'credit' => $maintenanceRecord->cost,
        ]);
    }

    /**
     * Record payroll expense for HR
     *
     * @param  mixed  $payroll
     * @return void
     */
    public function recordPayrollExpense($payroll)
    {
        // Create journal entries for the payroll expense
        $journalEntry = JournalEntry::create([
            'company_id' => $payroll->company_id,
            'date' => $payroll->period_end_date,
            'reference' => 'Payroll: '.$payroll->id,
            'description' => 'Payroll expense',
        ]);

        // Debit salary expense account
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('salary_expense'),
            'description' => 'Salary Expense',
            'debit' => $payroll->total_amount,
            'credit' => 0,
        ]);

        // Credit bank account
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('bank'),
            'description' => 'Salary Payment',
            'debit' => 0,
            'credit' => $payroll->total_amount,
        ]);
    }

    /**
     * Process a refund for a booking cancellation
     *
     * @return Payment|null
     */
    public function processBookingRefund(Booking $booking, float $refundAmount, string $reason = 'Cancellation')
    {
        // Find the original invoice for this booking
        $invoice = Invoice::where('hostel_id', $booking->hostel_id)
            ->where('customer_id', $booking->hostel_occupant_id)
            ->where('customer_type', 'hostel_occupant')
            ->first();

        if (! $invoice) {
            return null;
        }

        // Create a negative payment (refund) record
        $payment = Payment::create([
            'company_id' => $invoice->company_id,
            'invoice_id' => $invoice->id,
            'amount' => -$refundAmount, // Negative amount for refund
            'payment_date' => now(),
            'payment_method' => 'refund',
            'reference' => 'REF-'.date('Y').'-'.strtoupper(uniqid()),
            'notes' => $reason,
        ]);

        // Update invoice status based on new balance
        $totalPaid = $invoice->payments()->sum('amount');

        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partial']);
        } else {
            $invoice->update(['status' => 'pending']);
        }

        // Create journal entries for the refund (reverse the original entries)
        $journalEntry = JournalEntry::create([
            'company_id' => $payment->company_id,
            'date' => $payment->payment_date,
            'reference' => 'Refund: '.$payment->reference,
            'description' => 'Refund issued for booking cancellation: '.$reason,
        ]);

        // Debit revenue (reverse the original credit)
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('revenue', $payment->company_id),
            'description' => 'Revenue reversal for refund',
            'debit' => $refundAmount,
            'credit' => 0,
        ]);

        // Credit accounts receivable (reverse the original debit)
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('accounts_receivable', $payment->company_id),
            'description' => 'Accounts Receivable adjustment for refund',
            'debit' => 0,
            'credit' => $refundAmount,
        ]);

        // If refund was actually paid out (not just credit memo), debit bank account
        // We'll assume refunds are processed immediately for now
        \Modules\Finance\Models\JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('bank', $payment->company_id),
            'description' => 'Bank withdrawal for refund payment',
            'debit' => 0,
            'credit' => $refundAmount,
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
            'accounts_receivable' => ['code' => 'AR', 'type' => 'asset', 'name_like' => 'Receivable'],
            'accounts_payable' => ['code' => 'AP', 'type' => 'liability', 'name_like' => 'Payable'],
            'revenue' => ['code' => 'REV', 'type' => 'income', 'name_like' => 'Revenue'],
            'bank' => ['code' => 'BANK', 'type' => 'asset', 'name_like' => 'Bank'],
            'procurement_expense' => ['code' => 'PROCEXP', 'type' => 'expense', 'name_like' => 'Procurement'],
            'fuel_expense' => ['code' => 'FUELEXP', 'type' => 'expense', 'name_like' => 'Fuel'],
            'maintenance_expense' => ['code' => 'MAINTEXP', 'type' => 'expense', 'name_like' => 'Maintenance'],
            'salary_expense' => ['code' => 'SALARYEXP', 'type' => 'expense', 'name_like' => 'Salary'],
        ];

        $hint = $map[$accountType] ?? null;
        if (! $hint) {
            return Account::where('company_id', $companyId)->value('id') ?? 1;
        }

        $query = Account::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        $account = $query->where('code', $hint['code'])->first();
        if ($account) {
            return $account->id;
        }

        $query = Account::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        $account = $query->where('type', $hint['type'])->first();
        if ($account) {
            return $account->id;
        }

        $query = Account::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        $account = $query->where('name', 'like', '%'.$hint['name_like'].'%')->first();
        if ($account) {
            return $account->id;
        }

        return Account::where('company_id', $companyId)->value('id') ?? 1;
    }
}
