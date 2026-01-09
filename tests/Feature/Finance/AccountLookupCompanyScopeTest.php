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

it('uses company-scoped accounts when posting payment journals', function () {
    $companyA = Company::create(['name' => 'Alpha Co', 'slug' => 'alpha', 'type' => 'finance']);
    $companyB = Company::create(['name' => 'Beta Co', 'slug' => 'beta', 'type' => 'finance']);

    Account::create(['company_id' => $companyA->id, 'code' => 'AR', 'name' => 'Accounts Receivable', 'type' => 'asset']);
    Account::create(['company_id' => $companyA->id, 'code' => 'BANK', 'name' => 'Bank', 'type' => 'asset']);
    Account::create(['company_id' => $companyA->id, 'code' => 'REV', 'name' => 'Revenue', 'type' => 'income']);

    Account::create(['company_id' => $companyB->id, 'code' => 'AR', 'name' => 'Accounts Receivable B', 'type' => 'asset']);
    Account::create(['company_id' => $companyB->id, 'code' => 'BANK', 'name' => 'Bank B', 'type' => 'asset']);
    Account::create(['company_id' => $companyB->id, 'code' => 'REV', 'name' => 'Revenue B', 'type' => 'income']);

    $invoiceA = Invoice::create([
        'company_id' => $companyA->id,
        'customer_name' => 'Alice',
        'customer_type' => 'tenant',
        'invoice_number' => 'INV-A-001',
        'invoice_date' => now(),
        'due_date' => now()->addDays(7),
        'status' => 'pending',
        'sub_total' => 900,
        'tax_total' => 0,
        'total' => 900,
    ]);

    $intentA = PayIntent::create([
        'company_id' => $companyA->id,
        'provider' => 'manual',
        'payable_type' => Invoice::class,
        'payable_id' => $invoiceA->id,
        'reference' => 'REF-A',
        'amount' => 300,
        'currency' => 'GHS',
        'status' => 'successful',
        'metadata' => ['invoice_id' => $invoiceA->id],
    ]);

    $txnA = PayTransaction::create([
        'pay_intent_id' => $intentA->id,
        'company_id' => $companyA->id,
        'provider' => 'manual',
        'transaction_type' => 'payment',
        'amount' => 300,
        'currency' => 'GHS',
        'provider_transaction_id' => 'TX-A',
        'status' => 'successful',
        'raw_payload' => ['test' => true],
        'processed_at' => now(),
    ]);

    $service = new IntegrationService;
    $service->processPaymentTransaction($txnA);

    $entryA = JournalEntry::where('company_id', $companyA->id)->latest()->first();
    expect($entryA)->not->toBeNull();
    $linesA = JournalLine::where('journal_entry_id', $entryA->id)->get();
    expect($linesA->count())->toBe(2);
    $accountIdsA = $linesA->pluck('account_id')->all();
    foreach ($accountIdsA as $id) {
        $acct = Account::find($id);
        expect($acct->company_id)->toBe($companyA->id);
    }

    $invoiceB = Invoice::create([
        'company_id' => $companyB->id,
        'customer_name' => 'Bob',
        'customer_type' => 'tenant',
        'invoice_number' => 'INV-B-001',
        'invoice_date' => now(),
        'due_date' => now()->addDays(7),
        'status' => 'pending',
        'sub_total' => 1000,
        'tax_total' => 0,
        'total' => 1000,
    ]);

    $intentB = PayIntent::create([
        'company_id' => $companyB->id,
        'provider' => 'manual',
        'payable_type' => Invoice::class,
        'payable_id' => $invoiceB->id,
        'reference' => 'REF-B',
        'amount' => 1000,
        'currency' => 'GHS',
        'status' => 'successful',
        'metadata' => ['invoice_id' => $invoiceB->id],
    ]);

    $txnB = PayTransaction::create([
        'pay_intent_id' => $intentB->id,
        'company_id' => $companyB->id,
        'provider' => 'manual',
        'transaction_type' => 'payment',
        'amount' => 1000,
        'currency' => 'GHS',
        'provider_transaction_id' => 'TX-B',
        'status' => 'successful',
        'raw_payload' => ['test' => true],
        'processed_at' => now(),
    ]);

    $service->processPaymentTransaction($txnB);

    $entryB = JournalEntry::where('company_id', $companyB->id)->latest()->first();
    expect($entryB)->not->toBeNull();
    $linesB = JournalLine::where('journal_entry_id', $entryB->id)->get();
    expect($linesB->count())->toBe(2);
    $accountIdsB = $linesB->pluck('account_id')->all();
    foreach ($accountIdsB as $id) {
        $acct = Account::find($id);
        expect($acct->company_id)->toBe($companyB->id);
    }
});
