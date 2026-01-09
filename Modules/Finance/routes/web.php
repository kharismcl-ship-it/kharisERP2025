<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\ReceiptController;
use Modules\Finance\Http\Livewire\Accounts\ChartOfAccounts;
use Modules\Finance\Http\Livewire\FinanceIndex;
use Modules\Finance\Http\Livewire\Invoices\Create as InvoicesCreate;
use Modules\Finance\Http\Livewire\Invoices\Index as InvoicesIndex;
use Modules\Finance\Http\Livewire\Payments\Index as PaymentsIndex;
use Modules\Finance\Http\Livewire\Reports\FinancialReports;

Route::middleware(['web', 'auth', 'set-company:finance'])
    ->prefix('finance')
    ->name('finance.')
    ->group(function () {
        Route::get('/', FinanceIndex::class)->name('index');
        Route::get('/invoices', InvoicesIndex::class)->name('invoices.index');
        Route::get('/invoices/create', InvoicesCreate::class)->name('invoices.create');
        Route::get('/payments', PaymentsIndex::class)->name('payments.index');
        Route::get('/accounts/chart', ChartOfAccounts::class)->name('accounts.chart');
        Route::get('/reports', FinancialReports::class)->name('reports.index');

        // Receipt routes
        Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
        Route::get('/receipts/{receipt}/download', [ReceiptController::class, 'download'])->name('receipts.download');
        Route::post('/receipts/{receipt}/send-email', [ReceiptController::class, 'sendEmail'])->name('receipts.send-email');
    });
