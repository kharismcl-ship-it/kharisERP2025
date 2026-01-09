<?php

namespace Modules\Hostels\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\Hostels\Models\Deposit;
use Modules\Hostels\Models\HostelBillingCycle;
use Modules\Hostels\Models\HostelBillingRule;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\HostelUtilityCharge;
use Modules\Hostels\Models\Room;

class HostelBillingService
{
    public function generateRecurringBilling()
    {
        $today = Carbon::today();

        // Get active billing cycles that are due today
        $billingCycles = HostelBillingCycle::where('billing_date', $today)
            ->active()
            ->autoGenerate()
            ->with('hostel')
            ->get();

        foreach ($billingCycles as $cycle) {
            try {
                DB::beginTransaction();

                $this->processBillingCycle($cycle);

                DB::commit();

                Log::info("Successfully processed billing cycle: {$cycle->name} for hostel: {$cycle->hostel->name}");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to process billing cycle {$cycle->id}: ".$e->getMessage());
            }
        }
    }

    private function processBillingCycle(HostelBillingCycle $cycle)
    {
        // Get all active hostel occupants for this hostel
        $hostelOccupants = HostelOccupant::where('hostel_id', $cycle->hostel_id)
            ->where('status', 'active')
            ->with(['room', 'currentBooking'])
            ->get();

        foreach ($hostelOccupants as $hostelOccupant) {
            $this->generateHostelOccupantInvoice($hostelOccupant, $cycle);
        }

        // Update cycle dates for next period
        $this->updateBillingCycleDates($cycle);
    }

    private function generateHostelOccupantInvoice(HostelOccupant $hostelOccupant, HostelBillingCycle $cycle)
    {
        $invoiceData = [
            'company_id' => $hostelOccupant->hostel->company_id,
            'customer_name' => $hostelOccupant->full_name,
            'customer_type' => 'hostel_occupant',
            'customer_id' => $hostelOccupant->id,
            'invoice_number' => 'INV-'.date('Y').'-'.strtoupper(uniqid()),
            'invoice_date' => $cycle->billing_date,
            'due_date' => $cycle->due_date,
            'status' => 'draft',
            'sub_total' => 0,
            'tax_total' => 0,
            'total' => 0,
            'hostel_id' => $hostelOccupant->hostel_id,
            'reference' => "Billing Cycle: {$cycle->name}",
        ];

        $invoice = Invoice::create($invoiceData);
        $subTotal = 0;

        // Add room rent charge
        if ($hostelOccupant->room && $hostelOccupant->currentBooking) {
            $roomCharge = $this->addRoomRentCharge($invoice, $hostelOccupant, $cycle);
            $subTotal += $roomCharge;
        }

        // Add utility charges
        $utilityCharges = $this->addUtilityCharges($invoice, $hostelOccupant, $cycle);
        $subTotal += $utilityCharges;

        // Apply billing rules (late fees, discounts, etc.)
        $ruleCharges = $this->applyBillingRules($invoice, $tenant, $subTotal, $cycle);
        $subTotal += $ruleCharges;

        // Add deposit charges if configured
        $depositCharges = $this->addDepositCharges($invoice, $tenant, $cycle);
        $subTotal += $depositCharges;

        // Update invoice totals
        $invoice->update([
            'sub_total' => $subTotal,
            'total' => $subTotal,
            'status' => 'sent',
        ]);

        // Create journal entry for accounting
        $this->createInvoiceJournalEntry($invoice, $cycle);

        return $invoice;
    }

    private function addRoomRentCharge(Invoice $invoice, HostelOccupant $hostelOccupant, HostelBillingCycle $cycle): float
    {
        $booking = $hostelOccupant->currentBooking;
        $nightlyRate = $booking->total_amount / $booking->number_of_nights;
        $daysInCycle = $cycle->start_date->diffInDays($cycle->end_date) + 1;
        $chargeAmount = $nightlyRate * $daysInCycle;

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => "Room Rent: {$hostelOccupant->room->room_number} ({$cycle->name})",
            'quantity' => $daysInCycle,
            'unit_price' => $nightlyRate,
            'amount' => $chargeAmount,
            'gl_account_code' => '4000', // Revenue account
            'reference_type' => 'booking',
            'reference_id' => $booking->id,
        ]);

        return $chargeAmount;
    }

    private function addUtilityCharges(Invoice $invoice, HostelOccupant $hostelOccupant, HostelBillingCycle $cycle): float
    {
        $totalUtilityCharges = 0;

        // Get utility charges for this hostel occupant and billing cycle
        $utilityCharges = HostelUtilityCharge::where('hostel_occupant_id', $hostelOccupant->id)
            ->where('billing_cycle_id', $cycle->id)
            ->where('status', 'pending')
            ->get();

        foreach ($utilityCharges as $utility) {
            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => "{$utility->utility_type} Charge: ".$utility->billing_period_start->format('M Y'),
                'quantity' => 1,
                'unit_price' => $utility->total_amount,
                'amount' => $utility->total_amount,
                'gl_account_code' => $this->getUtilityGLAccount($utility->utility_type),
                'reference_type' => 'utility_charge',
                'reference_id' => $utility->id,
            ]);

            $utility->update(['status' => 'billed', 'invoice_id' => $invoice->id]);
            $totalUtilityCharges += $utility->total_amount;
        }

        return $totalUtilityCharges;
    }

    private function applyBillingRules(Invoice $invoice, HostelOccupant $hostelOccupant, float $subTotal, HostelBillingCycle $cycle): float
    {
        $totalRuleCharges = 0;

        $billingRules = HostelBillingRule::where('hostel_id', $hostelOccupant->hostel_id)
            ->active()
            ->autoApply()
            ->get();

        foreach ($billingRules as $rule) {
            $chargeAmount = $rule->calculateCharge($subTotal);

            if ($chargeAmount > 0) {
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'description' => "{$rule->name} - {$cycle->name}",
                    'quantity' => 1,
                    'unit_price' => $chargeAmount,
                    'amount' => $chargeAmount,
                    'gl_account_code' => $rule->gl_account_code,
                    'reference_type' => 'billing_rule',
                    'reference_id' => $rule->id,
                ]);

                $totalRuleCharges += $chargeAmount;
            }
        }

        return $totalRuleCharges;
    }

    private function updateBillingCycleDates(HostelBillingCycle $cycle)
    {
        $startDate = $cycle->end_date->copy()->addDay();
        $endDate = $this->calculateNextEndDate($startDate, $cycle->cycle_type);
        $billingDate = $endDate->copy();
        $dueDate = $billingDate->copy()->addDays(7);

        $cycle->update([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'billing_date' => $billingDate,
            'due_date' => $dueDate,
        ]);
    }

    private function calculateNextEndDate(Carbon $startDate, string $cycleType): Carbon
    {
        return match ($cycleType) {
            'monthly' => $startDate->copy()->endOfMonth(),
            'quarterly' => $startDate->copy()->addMonths(3)->subDay(),
            'semester' => $startDate->copy()->addMonths(6)->subDay(),
            default => $startDate->copy()->addMonth()->subDay()
        };
    }

    private function getUtilityGLAccount(string $utilityType): string
    {
        return match ($utilityType) {
            'electricity' => '5101',
            'water' => '5102',
            'internet' => '5103',
            'gas' => '5104',
            'maintenance' => '5105',
            'service' => '5106',
            default => '5100'
        };
    }

    /**
     * Add deposit charges to invoice if configured
     */
    private function addDepositCharges(Invoice $invoice, HostelOccupant $hostelOccupant, HostelBillingCycle $cycle): float
    {
        if (! $cycle->include_deposits) {
            return 0;
        }

        $totalDepositCharges = 0;

        // Check if hostel occupant has any pending deposits
        $pendingDeposits = Deposit::where('hostel_occupant_id', $hostelOccupant->id)
            ->where('status', Deposit::STATUS_PENDING)
            ->where('hostel_id', $hostelOccupant->hostel_id)
            ->get();

        foreach ($pendingDeposits as $deposit) {
            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => "Security Deposit: {$deposit->purpose}",
                'quantity' => 1,
                'unit_price' => $deposit->amount,
                'amount' => $deposit->amount,
                'gl_account_code' => '2310', // Security Deposits Liability
                'reference_type' => 'deposit',
                'reference_id' => $deposit->id,
            ]);

            $deposit->update([
                'status' => Deposit::STATUS_COLLECTED,
                'invoice_id' => $invoice->id,
                'collected_at' => now(),
            ]);

            $totalDepositCharges += $deposit->amount;

            // Create journal entry for deposit collection
            if ($cycle->auto_post_to_gl) {
                $this->createDepositJournalEntry($deposit, $invoice);
            }
        }

        return $totalDepositCharges;
    }

    /**
     * Create journal entry for deposit collection
     */
    public function createDepositJournalEntry(Deposit $deposit, Invoice $invoice): void
    {
        $journalEntry = JournalEntry::create([
            'company_id' => $invoice->company_id,
            'date' => now(),
            'reference' => 'Deposit: '.$deposit->id,
            'description' => 'Security deposit collected for tenant: '.$deposit->tenant->full_name,
        ]);

        // Debit bank/cash account
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('cash_bank', $invoice->company_id),
            'description' => 'Deposit collection',
            'debit' => $deposit->amount,
            'credit' => 0,
        ]);

        // Credit security deposits liability
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->getAccountId('security_deposits_liability', $invoice->company_id),
            'description' => 'Security deposit liability',
            'debit' => 0,
            'credit' => $deposit->amount,
        ]);

        $deposit->update(['journal_entry_id' => $journalEntry->id]);
    }

    /**
     * Create journal entry for invoice
     */
    private function createInvoiceJournalEntry(Invoice $invoice, HostelBillingCycle $cycle): void
    {
        if (! $cycle->auto_post_to_gl) {
            return;
        }

        try {
            DB::beginTransaction();

            $journalEntry = JournalEntry::create([
                'company_id' => $invoice->company_id,
                'date' => $invoice->invoice_date,
                'reference' => 'Invoice: '.$invoice->invoice_number,
                'description' => 'Hostel billing for tenant: '.$invoice->customer_name,
            ]);

            // Debit accounts receivable
            JournalLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $this->getAccountId('accounts_receivable', $invoice->company_id),
                'description' => 'Accounts receivable',
                'debit' => $invoice->total,
                'credit' => 0,
            ]);

            // Credit revenue accounts based on invoice lines
            $revenueAccounts = [];
            $expenseAccounts = [];
            $liabilityAccounts = [];

            foreach ($invoice->lines as $line) {
                $accountCode = $line->gl_account_code;

                // Categorize accounts based on their type
                if (str_starts_with($accountCode, '4')) { // Revenue accounts (4xxx)
                    if (! isset($revenueAccounts[$accountCode])) {
                        $revenueAccounts[$accountCode] = 0;
                    }
                    $revenueAccounts[$accountCode] += $line->amount;
                } elseif (str_starts_with($accountCode, '5')) { // Expense accounts (5xxx)
                    if (! isset($expenseAccounts[$accountCode])) {
                        $expenseAccounts[$accountCode] = 0;
                    }
                    $expenseAccounts[$accountCode] += $line->amount;
                } elseif (str_starts_with($accountCode, '2')) { // Liability accounts (2xxx)
                    if (! isset($liabilityAccounts[$accountCode])) {
                        $liabilityAccounts[$accountCode] = 0;
                    }
                    $liabilityAccounts[$accountCode] += $line->amount;
                }
            }

            // Credit revenue accounts
            foreach ($revenueAccounts as $accountCode => $amount) {
                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $this->getAccountIdByCode($accountCode, $invoice->company_id),
                    'description' => 'Revenue from '.$this->getAccountDescription($accountCode),
                    'debit' => 0,
                    'credit' => $amount,
                ]);
            }

            // Debit expense accounts (for utility charges passed through to tenants)
            foreach ($expenseAccounts as $accountCode => $amount) {
                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $this->getAccountIdByCode($accountCode, $invoice->company_id),
                    'description' => 'Expense: '.$this->getAccountDescription($accountCode),
                    'debit' => $amount,
                    'credit' => 0,
                ]);
            }

            // Credit liability accounts (for security deposits)
            foreach ($liabilityAccounts as $accountCode => $amount) {
                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $this->getAccountIdByCode($accountCode, $invoice->company_id),
                    'description' => 'Liability: '.$this->getAccountDescription($accountCode),
                    'debit' => 0,
                    'credit' => $amount,
                ]);
            }

            $invoice->update(['journal_entry_id' => $journalEntry->id]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create journal entry for invoice {$invoice->id}: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Get account ID by type
     */
    private function getAccountId(string $accountType, $companyId): int
    {
        $accountMappings = [
            'security_deposits_liability' => 2310,
            'cash_bank' => 1110,
            'accounts_receivable' => 1310,
            'revenue' => 4110,
        ];

        return $accountMappings[$accountType] ?? 9999; // Default suspense account
    }

    /**
     * Get account ID by account code
     */
    private function getAccountIdByCode(string $accountCode, $companyId): int
    {
        $codeMappings = [
            '4000' => 4110, // Room revenue
            '5101' => 5110, // Electricity expense
            '5102' => 5120, // Water expense
            '5103' => 5130, // Internet expense
            '5104' => 5140, // Gas expense
            '5105' => 5150, // Maintenance expense
            '5106' => 5160, // Service expense
        ];

        return $codeMappings[$accountCode] ?? 9999; // Default suspense account
    }

    /**
     * Get account description by code
     */
    private function getAccountDescription(string $accountCode): string
    {
        return match ($accountCode) {
            '4000' => 'Room Revenue',
            '5101' => 'Electricity',
            '5102' => 'Water',
            '5103' => 'Internet',
            '5104' => 'Gas',
            '5105' => 'Maintenance',
            '5106' => 'Service',
            default => 'Miscellaneous'
        };
    }

    /**
     * Create journal entry for utility charge payment
     */
    public function createUtilityPaymentJournalEntry(HostelUtilityCharge $utilityCharge, ?Invoice $invoice = null): void
    {
        try {
            DB::beginTransaction();

            $journalEntry = JournalEntry::create([
                'company_id' => $utilityCharge->hostel->company_id,
                'date' => now(),
                'reference' => 'Utility: '.$utilityCharge->id,
                'description' => 'Utility payment: '.$utilityCharge->utility_type.' for '.$utilityCharge->tenant->full_name,
            ]);

            // Debit utility expense account
            JournalLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $this->getAccountIdByCode($this->getUtilityGLAccount($utilityCharge->utility_type), $utilityCharge->hostel->company_id),
                'description' => 'Utility expense: '.$utilityCharge->utility_type,
                'debit' => $utilityCharge->total_amount,
                'credit' => 0,
            ]);

            // Credit cash/bank account
            JournalLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $this->getAccountId('cash_bank', $utilityCharge->hostel->company_id),
                'description' => 'Utility payment',
                'debit' => 0,
                'credit' => $utilityCharge->total_amount,
            ]);

            $utilityCharge->update(['journal_entry_id' => $journalEntry->id]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create journal entry for utility charge {$utilityCharge->id}: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Create journal entry for deposit refund
     */
    public function createDepositRefundJournalEntry(Deposit $deposit): void
    {
        try {
            DB::beginTransaction();

            $journalEntry = JournalEntry::create([
                'company_id' => $deposit->hostel->company_id,
                'date' => now(),
                'reference' => 'Deposit Refund: '.$deposit->id,
                'description' => 'Security deposit refund for '.$deposit->tenant->full_name,
            ]);

            // Debit security deposits liability
            JournalLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $this->getAccountId('security_deposits_liability', $deposit->hostel->company_id),
                'description' => 'Security deposit liability reduction',
                'debit' => $deposit->refund_amount,
                'credit' => 0,
            ]);

            // Credit cash/bank account
            JournalLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $this->getAccountId('cash_bank', $deposit->hostel->company_id),
                'description' => 'Deposit refund payment',
                'debit' => 0,
                'credit' => $deposit->refund_amount,
            ]);

            // If there are deductions, create expense entry
            if ($deposit->deductions > 0) {
                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $this->getAccountIdByCode('5200', $deposit->hostel->company_id), // Damage/Repair expense
                    'description' => 'Damage/repair charges from deposit',
                    'debit' => $deposit->deductions,
                    'credit' => 0,
                ]);

                // Credit security deposits liability for deductions
                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $this->getAccountId('security_deposits_liability', $deposit->hostel->company_id),
                    'description' => 'Security deposit deductions',
                    'debit' => 0,
                    'credit' => $deposit->deductions,
                ]);
            }

            $deposit->update(['journal_entry_id' => $journalEntry->id]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create journal entry for deposit refund {$deposit->id}: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Create journal entry for late fee payment
     */
    public function createLateFeeJournalEntry(float $amount, HostelOccupant $hostelOccupant, string $description): void
    {
        try {
            DB::beginTransaction();

            $journalEntry = JournalEntry::create([
                'company_id' => $hostelOccupant->hostel->company_id,
                'date' => now(),
                'reference' => 'Late Fee: '.$hostelOccupant->id,
                'description' => $description,
            ]);

            // Debit late fee income account
            JournalLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $this->getAccountIdByCode('4900', $hostelOccupant->hostel->company_id), // Late Fee Income
                'description' => 'Late fee income',
                'debit' => $amount,
                'credit' => 0,
            ]);

            // Credit accounts receivable
            JournalLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $this->getAccountId('accounts_receivable', $hostelOccupant->hostel->company_id),
                'description' => 'Late fee receivable',
                'debit' => 0,
                'credit' => $amount,
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create journal entry for late fee: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Enhanced account mapping with more comprehensive GL accounts
     */
    private function getEnhancedAccountMappings(): array
    {
        return [
            'security_deposits_liability' => 2310,
            'cash_bank' => 1110,
            'accounts_receivable' => 1310,
            'revenue_room' => 4110,
            'revenue_utility' => 4120,
            'expense_electricity' => 5110,
            'expense_water' => 5120,
            'expense_internet' => 5130,
            'expense_gas' => 5140,
            'expense_maintenance' => 5150,
            'expense_service' => 5160,
            'income_late_fee' => 4900,
            'expense_damage_repair' => 5200,
            'suspense_account' => 9999,
        ];
    }
}
