<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Company;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\Finance\Services\IntegrationService;
use Modules\PaymentsChannel\Models\PayIntent;
use Modules\PaymentsChannel\Models\PayTransaction;

uses(RefreshDatabase::class);

it('scopes GL account lookup by company', function () {
    $companyA = Company::create(['name' => 'Alpha Co', 'slug' => 'alpha', 'type' => 'ops']);
    $companyB = Company::create(['name' => 'Beta Co', 'slug' => 'beta', 'type' => 'ops']);

    $arA = Account::create(['company_id' => $companyA->id, 'code' => 'AR', 'name' => 'Accounts Receivable A', 'type' => 'asset']);
    $bankA = Account::create(['company_id' => $companyA->id, 'code' => 'BANK', 'name' => 'Bank A', 'type' => 'asset']);
    $revA = Account::create(['company_id' => $companyA->id, 'code' => 'REV', 'name' => 'Revenue A', 'type' => 'income']);

    Account::create(['company_id' => $companyB->id, 'code' => 'AR', 'name' => 'Accounts Receivable B', 'type' => 'asset']);
    Account::create(['company_id' => $companyB->id, 'code' => 'BANK', 'name' => 'Bank B', 'type' => 'asset']);
    Account::create(['company_id' => $companyB->id, 'code' => 'REV', 'name' => 'Revenue B', 'type' => 'income']);

    $invoiceA = Invoice::create([
        'company_id' => $companyA->id,
        'customer_name' => 'Client A',
        'customer_type' => 'tenant',
        'invoice_number' => 'INV-A-001',
        'invoice_date' => now(),
        'due_date' => now()->addDays(7),
        'status' => 'pending',
        'sub_total' => 100,
        'tax_total' => 0,
        'total' => 100,
    ]);

    $intentA = PayIntent::create([
        'company_id' => $companyA->id,
        'provider' => 'manual',
        'pay_method_id' => null,
        'payable_type' => Invoice::class,
        'payable_id' => $invoiceA->id,
        'reference' => 'REF-A-1',
        'provider_reference' => null,
        'amount' => 60,
        'currency' => 'GHS',
        'status' => 'successful',
        'description' => null,
        'customer_name' => 'Client A',
        'customer_email' => null,
        'customer_phone' => null,
        'return_url' => null,
        'callback_url' => null,
        'metadata' => ['invoice_id' => $invoiceA->id],
        'expires_at' => null,
    ]);

    $txnA = PayTransaction::create([
        'pay_intent_id' => $intentA->id,
        'company_id' => $companyA->id,
        'provider' => 'manual',
        'transaction_type' => 'payment',
        'amount' => 60,
        'currency' => 'GHS',
        'provider_transaction_id' => 'TX-A-1',
        'status' => 'successful',
        'raw_payload' => ['ok' => true],
        'processed_at' => now(),
    ]);

    $service = app(IntegrationService::class);
    $service->processPaymentTransaction($txnA);

    $invoiceA->refresh();
    expect($invoiceA->status)->toBe('partial');

    $entryA = JournalEntry::where('company_id', $companyA->id)->latest()->first();
    expect($entryA)->not()->toBeNull();

    $lines = JournalLine::where('journal_entry_id', $entryA->id)->get();
    expect($lines->count())->toBe(2);
    expect($lines->firstWhere('debit', '>', 0)->account_id)->toBe($bankA->id);
    expect($lines->firstWhere('credit', '>', 0)->account_id)->toBe($arA->id);
});
