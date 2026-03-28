<?php

namespace Modules\Finance\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Finance\Filament\Pages\ApAgingReport;
use Modules\Finance\Filament\Pages\ArAgingReport;
use Modules\Finance\Filament\Pages\BalanceSheet;
use Modules\Finance\Filament\Pages\BudgetVsActualReport;
use Modules\Finance\Filament\Pages\CashFlowStatement;
use Modules\Finance\Filament\Pages\ConsolidatedReport;
use Modules\Finance\Filament\Pages\CostCentreReport;
use Modules\Finance\Filament\Pages\CustomerStatementReport;
use Modules\Finance\Filament\Pages\FinanceDashboard;
use Modules\Finance\Filament\Pages\FinancialRatiosPage;
use Modules\Finance\Filament\Pages\FixedAssetMapPage;
use Modules\Finance\Filament\Pages\GeneralLedger;
use Modules\Finance\Filament\Pages\IncomeStatement;
use Modules\Finance\Filament\Pages\TrialBalance;
use Modules\Finance\Filament\Pages\VatReturnReport;
use Modules\Finance\Filament\Pages\WhtCertificatePage;
use Modules\Finance\Filament\Resources\AccountingPeriodResource;
use Modules\Finance\Filament\Resources\AccountResource;
use Modules\Finance\Filament\Resources\AdvancePaymentResource;
use Modules\Finance\Filament\Resources\AssetCategoryResource;
use Modules\Finance\Filament\Resources\BankAccountResource;
use Modules\Finance\Filament\Resources\BankReconciliationResource;
use Modules\Finance\Filament\Resources\BudgetResource;
use Modules\Finance\Filament\Resources\ChequeResource;
use Modules\Finance\Filament\Resources\CostCentreResource;
use Modules\Finance\Filament\Resources\CreditNoteResource;
use Modules\Finance\Filament\Resources\CurrencyResource;
use Modules\Finance\Filament\Resources\CustomerResource;
use Modules\Finance\Filament\Resources\ExpenseCategoryResource;
use Modules\Finance\Filament\Resources\ExpenseClaimResource;
use Modules\Finance\Filament\Resources\FixedAssetResource;
use Modules\Finance\Filament\Resources\FxRateResource;
use Modules\Finance\Filament\Resources\InvoiceLineResource;
use Modules\Finance\Filament\Resources\InvoiceReminderRuleResource;
use Modules\Finance\Filament\Resources\InvoiceResource;
use Modules\Finance\Filament\Resources\JournalEntryLogResource;
use Modules\Finance\Filament\Resources\JournalEntryResource;
use Modules\Finance\Filament\Resources\JournalLineResource;
use Modules\Finance\Filament\Resources\PaymentAllocationResource;
use Modules\Finance\Filament\Resources\PaymentBatchResource;
use Modules\Finance\Filament\Resources\PaymentResource;
use Modules\Finance\Filament\Resources\PettyCashResource;
use Modules\Finance\Filament\Resources\ReceiptResource;
use Modules\Finance\Filament\Resources\RecurringInvoiceResource;
use Modules\Finance\Filament\Resources\TaxRateResource;

class FinanceFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'finance';
    }

    public function register(Panel $panel): void
    {
        $resources = [
            // Billing
            InvoiceResource::class,
            PaymentResource::class,
            ReceiptResource::class,
            RecurringInvoiceResource::class,
            CustomerResource::class,
            CreditNoteResource::class,
            ExpenseClaimResource::class,
            InvoiceReminderRuleResource::class,
            PaymentBatchResource::class,
            PaymentAllocationResource::class,
            AdvancePaymentResource::class,

            // General Ledger
            AccountResource::class,
            JournalEntryResource::class,
            TaxRateResource::class,
            AccountingPeriodResource::class,
            CostCentreResource::class,
            BankAccountResource::class,
            BankReconciliationResource::class,
            ExpenseCategoryResource::class,
            PettyCashResource::class,
            BudgetResource::class,
            CurrencyResource::class,
            FxRateResource::class,
            ChequeResource::class,
            JournalEntryLogResource::class,

            // Fixed Assets
            AssetCategoryResource::class,
            FixedAssetResource::class,

            // Hidden from nav (accessed via relation managers)
            InvoiceLineResource::class,
            JournalLineResource::class,
        ];

        if (in_array($panel->getId(), ['admin', 'company-admin'])) {
            $panel->resources($resources);
        }

        $panel->pages([
            // Dashboards & core reports
            FinanceDashboard::class,
            TrialBalance::class,
            IncomeStatement::class,
            ArAgingReport::class,
            BalanceSheet::class,
            ApAgingReport::class,
            GeneralLedger::class,
            CashFlowStatement::class,
            FixedAssetMapPage::class,

            // New reports (Phase 2 & 3 & 4)
            BudgetVsActualReport::class,
            VatReturnReport::class,
            CustomerStatementReport::class,
            CostCentreReport::class,
            ConsolidatedReport::class,
            FinancialRatiosPage::class,
            WhtCertificatePage::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}