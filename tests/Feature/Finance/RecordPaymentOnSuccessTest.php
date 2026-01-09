<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Company;
use Modules\Finance\Listeners\Payments\RecordPaymentOnSuccess;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\Finance\Models\Payment;
use Modules\PaymentsChannel\Events\PaymentSucceeded;
use Modules\PaymentsChannel\Models\PayIntent;
use Modules\PaymentsChannel\Models\PayTransaction;

uses(RefreshDatabase::class);

it('records payment and updates invoice on PaymentSucceeded', function () {
    $company = Company::create(['name' => 'Test Co', 'slug' => 'test-co', 'type' => 'finance']);

    Account::create(['company_id' => $company->id, 'code' => 'AR', 'name' => 'Accounts Receivable', 'type' => 'asset']);
    Account::create(['company_id' => $company->id, 'code' => 'BANK', 'name' => 'Bank', 'type' => 'asset']);
    Account::create(['company_id' => $company->id, 'code' => 'REV', 'name' => 'Revenue', 'type' => 'income']);

    $invoice = Invoice::create([
        'company_id' => $company->id,
        'customer_name' => 'John Doe',
        'customer_type' => 'tenant',
        'invoice_number' => 'INV-TEST-001',
        'invoice_date' => now(),
        'due_date' => now()->addDays(7),
        'status' => 'pending',
        'sub_total' => 1000,
        'tax_total' => 0,
        'total' => 1000,
    ]);

    $intent = PayIntent::create([
        'company_id' => $company->id,
        'provider' => 'manual',
        'payable_type' => Invoice::class,
        'payable_id' => $invoice->id,
        'reference' => 'REF-123',
        'amount' => 500,
        'currency' => 'GHS',
        'status' => 'successful',
        'metadata' => ['invoice_id' => $invoice->id],
    ]);

    $transaction = PayTransaction::create([
        'pay_intent_id' => $intent->id,
        'company_id' => $company->id,
        'provider' => 'manual',
        'transaction_type' => 'payment',
        'amount' => 500,
        'currency' => 'GHS',
        'provider_transaction_id' => 'TX-123',
        'status' => 'successful',
        'raw_payload' => ['test' => true],
        'processed_at' => now(),
    ]);

    $event = new PaymentSucceeded($intent);
    $listener = new RecordPaymentOnSuccess;
    $listener->handle($event);

    $invoice->refresh();
    expect($invoice->status)->toBe('partial');

    $payment = Payment::where('invoice_id', $invoice->id)->first();
    expect($payment)->not->toBeNull();
    expect((float) $payment->amount)->toBe(500.0);

    $journalEntry = JournalEntry::where('company_id', $company->id)->first();
    expect($journalEntry)->not->toBeNull();

    $lines = JournalLine::where('journal_entry_id', $journalEntry->id)->get();
    expect($lines->count())->toBe(2);
    $debit = (float) $lines->firstWhere('debit', '>', 0)?->debit;
    $credit = (float) $lines->firstWhere('credit', '>', 0)?->credit;
    expect($debit)->toBe(500.0);
    expect($credit)->toBe(500.0);
});
