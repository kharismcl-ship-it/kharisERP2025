<?php

namespace Modules\Finance\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Finance\Filament\Pages\ArAgingReport;
use Modules\Finance\Filament\Pages\FinanceDashboard;
use Modules\Finance\Filament\Pages\IncomeStatement;
use Modules\Finance\Filament\Pages\TrialBalance;
use Modules\Finance\Filament\Resources\AccountResource;
use Modules\Finance\Filament\Resources\InvoiceLineResource;
use Modules\Finance\Filament\Resources\InvoiceResource;
use Modules\Finance\Filament\Resources\JournalEntryResource;
use Modules\Finance\Filament\Resources\JournalLineResource;
use Modules\Finance\Filament\Resources\PaymentResource;
use Modules\Finance\Filament\Resources\ReceiptResource;

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
        if ($panel->getId() === 'admin') {
            $panel->resources([
                // Billing
                InvoiceResource::class,
                PaymentResource::class,
                ReceiptResource::class,

                // General Ledger
                AccountResource::class,
                JournalEntryResource::class,

                // Hidden from nav (accessed via relation managers)
                InvoiceLineResource::class,
                JournalLineResource::class,
            ]);
        } elseif ($panel->getId() === 'company-admin') {
            $panel->resources([
                // Billing
                InvoiceResource::class,
                PaymentResource::class,
                ReceiptResource::class,

                // General Ledger
                AccountResource::class,
                JournalEntryResource::class,

                // Hidden from nav (accessed via relation managers)
                InvoiceLineResource::class,
                JournalLineResource::class,
            ]);
        }

        $panel->pages([
            FinanceDashboard::class,
            TrialBalance::class,
            IncomeStatement::class,
            ArAgingReport::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
