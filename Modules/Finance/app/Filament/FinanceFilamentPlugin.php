<?php

namespace Modules\Finance\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Finance\Filament\Pages\ApAgingReport;
use Modules\Finance\Filament\Pages\ArAgingReport;
use Modules\Finance\Filament\Pages\BalanceSheet;
use Modules\Finance\Filament\Pages\CashFlowStatement;
use Modules\Finance\Filament\Pages\FinanceDashboard;
use Modules\Finance\Filament\Pages\GeneralLedger;
use Modules\Finance\Filament\Pages\IncomeStatement;
use Modules\Finance\Filament\Pages\TrialBalance;
use Modules\Finance\Filament\Resources\AccountResource;
use Modules\Finance\Filament\Resources\AccountingPeriodResource;
use Modules\Finance\Filament\Resources\AssetCategoryResource;
use Modules\Finance\Filament\Resources\BankAccountResource;
use Modules\Finance\Filament\Resources\BankReconciliationResource;
use Modules\Finance\Filament\Resources\CostCentreResource;
use Modules\Finance\Filament\Resources\FixedAssetResource;
use Modules\Finance\Filament\Resources\InvoiceLineResource;
use Modules\Finance\Filament\Resources\InvoiceResource;
use Modules\Finance\Filament\Resources\JournalEntryResource;
use Modules\Finance\Filament\Resources\JournalLineResource;
use Modules\Finance\Filament\Resources\PaymentResource;
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

            // General Ledger
            AccountResource::class,
            JournalEntryResource::class,
            TaxRateResource::class,
            AccountingPeriodResource::class,
            CostCentreResource::class,
            BankAccountResource::class,
            BankReconciliationResource::class,

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
            FinanceDashboard::class,
            TrialBalance::class,
            IncomeStatement::class,
            ArAgingReport::class,
            BalanceSheet::class,
            ApAgingReport::class,
            GeneralLedger::class,
            CashFlowStatement::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
