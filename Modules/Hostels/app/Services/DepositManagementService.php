<?php

namespace Modules\Hostels\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\Finance\Services\IntegrationService;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Deposit;
use Modules\Hostels\Models\Hostel;

class DepositManagementService
{
    protected $integrationService;

    protected $billingService;

    public function __construct(?IntegrationService $integrationService = null, ?HostelBillingService $billingService = null)
    {
        $this->integrationService = $integrationService ?? app(IntegrationService::class);
        $this->billingService = $billingService ?? app(HostelBillingService::class);
    }

    /**
     * Create a deposit record for a booking
     */
    public function createDepositForBooking(Booking $booking, string $depositType = 'security'): ?Deposit
    {
        try {
            $hostel = $booking->hostel;

            if (! $hostel->require_deposit) {
                return null;
            }

            $depositAmount = $this->calculateDepositAmount($hostel, $booking->total_amount);

            if ($depositAmount <= 0) {
                return null;
            }

            return Deposit::create([
                'hostel_occupant_id' => $booking->hostel_occupant_id,
                'booking_id' => $booking->id,
                'hostel_id' => $booking->hostel_id,
                'amount' => $depositAmount,
                'deposit_type' => $depositType,
                'status' => Deposit::STATUS_PENDING,
                'notes' => "Deposit for booking #{$booking->booking_number}",
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to create deposit for booking {$booking->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Calculate deposit amount based on hostel configuration
     */
    public function calculateDepositAmount(Hostel $hostel, float $bookingAmount): float
    {
        if (! $hostel->require_deposit) {
            return 0;
        }

        if ($hostel->deposit_type === 'percentage') {
            return $bookingAmount * ($hostel->deposit_percentage / 100);
        }

        return $hostel->deposit_amount ?? 0;
    }

    /**
     * Mark deposit as collected and create accounting entries
     */
    public function collectDeposit(Deposit $deposit, array $paymentData = []): bool
    {
        try {
            DB::beginTransaction();

            // Create invoice for deposit collection
            $invoice = $this->createDepositInvoice($deposit);

            // Create journal entry for accounting using HostelBillingService
            $this->billingService->createDepositJournalEntry($deposit, $invoice);

            $deposit->markAsCollected($invoice->id);

            DB::commit();

            Log::info("Deposit {$deposit->id} collected successfully");

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to collect deposit {$deposit->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Process deposit refund
     */
    public function processRefund(Deposit $deposit, float $refundAmount, array $deductions = [], ?string $reason = null): bool
    {
        try {
            DB::beginTransaction();

            $totalDeductions = array_sum(array_column($deductions, 'amount'));
            $deductionReasons = array_column($deductions, 'reason');

            // Process the refund in the deposit model
            $deposit->processRefund($refundAmount, $totalDeductions, $deductionReasons);

            // Create journal entry for refund using HostelBillingService
            $this->billingService->createDepositRefundJournalEntry($deposit);

            DB::commit();

            Log::info("Deposit {$deposit->id} refund processed: {$refundAmount}");

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to process refund for deposit {$deposit->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Create invoice for deposit collection
     */
    protected function createDepositInvoice(Deposit $deposit): Invoice
    {
        return Invoice::create([
            'company_id' => $deposit->hostel->company_id,
            'customer_name' => $deposit->occupant->full_name,
            'customer_type' => 'hostel_occupant',
            'customer_id' => $deposit->hostel_occupant_id,
            'invoice_number' => 'DEP-'.date('Y').'-'.strtoupper(uniqid()),
            'invoice_date' => Carbon::today(),
            'due_date' => Carbon::today(),
            'status' => 'paid',
            'sub_total' => $deposit->amount,
            'tax_total' => 0,
            'total' => $deposit->amount,
            'hostel_id' => $deposit->hostel_id,
            'reference' => "Security Deposit - {$deposit->deposit_type}",
            'notes' => $deposit->notes,
        ]);
    }

    /**
     * Create journal entry for deposit collection
     */
    protected function createDepositJournalEntry(Deposit $deposit, Invoice $invoice): JournalEntry
    {
        $description = "Security Deposit Collection - {$deposit->hostelOccupant->full_name} - Booking #{$deposit->booking->booking_number}";

        $journalEntry = JournalEntry::create([
            'company_id' => $deposit->hostel->company_id,
            'date' => Carbon::today(),
            'reference' => $invoice->invoice_number,
            'description' => $description,
        ]);

        // Credit Security Deposits Liability
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('security_deposits_liability', $deposit->hostel->company_id),
            'description' => $description,
            'debit' => 0,
            'credit' => $deposit->amount,
        ]);

        // Debit Cash/Bank account
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('cash_bank', $deposit->hostel->company_id),
            'description' => $description,
            'debit' => $deposit->amount,
            'credit' => 0,
        ]);

        return $journalEntry;
    }

    /**
     * Create journal entry for deposit refund
     */
    protected function createRefundJournalEntry(Deposit $deposit, float $refundAmount, float $deductions): JournalEntry
    {
        $description = "Security Deposit Refund - {$deposit->hostelOccupant->full_name}";

        $journalEntry = JournalEntry::create([
            'company_id' => $deposit->hostel->company_id,
            'date' => Carbon::today(),
            'reference' => "DEP-REFUND-{$deposit->id}",
            'description' => $description,
        ]);

        // Debit Security Deposits Liability (reversal)
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('security_deposits_liability', $deposit->hostel->company_id),
            'description' => 'Reversal of security deposit liability',
            'debit' => $deposit->amount,
            'credit' => 0,
        ]);

        if ($refundAmount > 0) {
            // Credit Cash/Bank account (refund payment)
            JournalLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $this->getAccountId('cash_bank', $deposit->hostel->company_id),
                'description' => 'Deposit refund payment',
                'debit' => 0,
                'credit' => $refundAmount,
            ]);
        }

        if ($deductions > 0) {
            // Credit Other Income - Deposit Forfeiture
            JournalLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $this->getAccountId('other_income', $deposit->hostel->company_id),
                'description' => 'Deposit deductions/forfeiture',
                'debit' => 0,
                'credit' => $deductions,
            ]);
        }

        return $journalEntry;
    }

    /**
     * Get account ID by type and company
     */
    protected function getAccountId(string $accountType, $companyId): int
    {
        // Default account mappings - in a real implementation, this would come from configuration
        $accountMappings = [
            'security_deposits_liability' => 2310,
            'cash_bank' => 1110,
            'other_income' => 5310,
            'accounts_receivable' => 1310,
            'revenue' => 4110,
        ];

        return $accountMappings[$accountType] ?? 9999; // Default to suspense account if not found
    }

    /**
     * Get deposits due for refund (after checkout)
     */
    public function getDepositsDueForRefund(): \Illuminate\Database\Eloquent\Collection
    {
        return Deposit::where('status', Deposit::STATUS_COLLECTED)
            ->whereHas('booking', function ($query) {
                $query->where('check_out_date', '<=', Carbon::today()->subDays(7))
                    ->where('status', 'checked_out');
            })
            ->with(['hostelOccupant', 'booking', 'hostel'])
            ->get();
    }

    /**
     * Auto-process deposit refunds for completed bookings
     */
    public function processAutoRefunds(): int
    {
        $processed = 0;
        $deposits = $this->getDepositsDueForRefund();

        foreach ($deposits as $deposit) {
            if ($this->processRefund($deposit, $deposit->amount)) {
                $processed++;
            }
        }

        return $processed;
    }
}
