
## Module Name

**PaymentChannel**

## Module Summary

The **PaymentChannel** module is a **universal payment engine** for the entire Kharis ERP.

It centralizes **all online payments** from any module (Hostels, Farms, Construction, Manufacturing, HR/Payroll, Finance, etc.) and supports multiple gateways:

* **Flutterwave**
* **Paystack**
* **PaySwitch**
* **Stripe**
* **GhanaPay**
* **Cash** (for offline records)
* **Bank Transfer** (for offline records)

No business module should integrate directly with any payment gateway.
All payment flows – initializing, redirecting, confirming, webhooks, refunds – must go through **PaymentChannel**.

---

## 1. Folder Structure

```text
Modules/PaymentChannel/
    Config/
    Database/
        migrations/
    Providers/
        PaymentChannelServiceProvider.php
        EventServiceProvider.php
    Http/
        Controllers/
            WebhookController.php
        Livewire/
            // optional UI for payment history if needed
    Models/
        PayProviderConfig.php
        PayMethod.php
        PayIntent.php
        PayTransaction.php
    Resources/
        views/
            checkout/   // reusable generic checkout views if needed
    Services/
        PaymentService.php
        Gateway/
            PaymentGatewayInterface.php
            FlutterwaveGateway.php
            PaystackGateway.php
            PaySwitchGateway.php
            StripeGateway.php
            GhanaPayGateway.php
            ManualGateway.php
    Traits/
        HasPayments.php
    Filament/
        Resources/
            PayProviderConfigResource.php
            PayMethodResource.php
            PayIntentResource.php
            PayTransactionResource.php
    module.json
    Routes/
        web.php
        api.php
```

---

## 2. Module Responsibilities

### 2.1 What PaymentChannel Does

* Stores **gateway configuration** per company (keys, secrets, modes).

* Defines **supported payment methods** for each company/module (card, Momo, bank, GhanaPay, etc.).

* Exposes a simple **PaymentService** API to other modules to:

    * Create **payment intents** (for invoices, bookings, orders, etc.).
    * Generate redirect URLs or inline payment data.
    * Confirm/check payment status.

* Handles **webhooks** from all gateways.

* Logs all **transactions** and statuses.

* Supports **multi-currency**, **multi-gateway**, and **fallbacks** (e.g. try Paystack then Flutterwave).

* Provides admin Filament UI to manage providers, methods, and view transactions.

### 2.2 What Other Modules Must Not Do

* No direct HTTP/API calls to Flutterwave, Paystack, PaySwitch, Stripe, GhanaPay.
* No direct storing of gateway secrets in other modules.
* No custom callback URLs per module; everything goes via PaymentChannel.

---

## 3. Data Model & Migrations

### 3.1 `PayProviderConfig`

**Table:** `pay_provider_configs`

Stores gateway credentials and settings.

Fields:

* `id`

* `company_id` (nullable → global default)

* `provider` (enum/string: `flutterwave`, `paystack`, `payswitch`, `stripe`, `ghanapay`, `manual`)

* `name` (display name: “Kharis Flutterwave”, “Kharis Paystack”)

* `is_default` (boolean)

* `is_active` (boolean)

* `mode` (enum: `sandbox`, `live`)

* `config` (json) – gateway-specific, e.g.:

    * Flutterwave: `{ "public_key": "", "secret_key": "", "encryption_key": "" }`
    * Paystack: `{ "public_key": "", "secret_key": "" }`
    * PaySwitch: `{ "api_key": "", "merchant_id": "" }`
    * Stripe: `{ "public_key": "", "secret_key": "" }`
    * GhanaPay: `{ "api_key": "", "institution_id": "" }`

* timestamps

Index: `{company_id, provider}`

---

### 3.2 `PayMethod`

**Table:** `pay_methods`

Represents logical payment methods visible to users, mapped to providers.

Fields:

* `id`
* `company_id`
* `code` (string, unique per company: `card`, `momo`, `bank_transfer`, `ghanapay`, `stripe_card`, etc.)
* `name` (e.g. “Card (Flutterwave)”, “Momo (Paystack)”, “GhanaPay Wallet”)
* `provider` (`flutterwave`, `paystack`, `payswitch`, `stripe`, `ghanapay`, `manual`)
* `channel` (string: `card`, `momo`, `bank`, `wallet`)
* `currency` (nullable, e.g. `GHS`, `USD`)
* `is_active` (boolean)
* `sort_order` (int)
* `config` (json, nullable) – method-specific config like allowed banks, Momo networks.
* timestamps

Use `PayMethod` to populate front-end options.

---

### 3.3 `PayIntent`

**Table:** `pay_intents`

Represents a **payment attempt/intent** created by another module.

Fields:

* `id`
* `company_id`
* `provider` (`flutterwave`, `paystack`, `payswitch`, `stripe`, `ghanapay`, `manual`)
* `pay_method_id` (nullable, FK)
* `payable_type` (polymorphic) – e.g. `Modules\Hostels\Models\HostelBooking`, `Modules\Finance\Models\Invoice`
* `payable_id`
* `reference` (internal unique reference, e.g. `PMT-2025-00001`)
* `provider_reference` (nullable) – e.g. Flutterwave `tx_ref`, Stripe `payment_intent_id`
* `amount` (decimal)
* `currency` (string, e.g. `GHS`, `USD`)
* `status` (enum: `pending`, `initiated`, `processing`, `successful`, `failed`, `cancelled`, `expired`)
* `description` (nullable)
* `customer_name` (nullable)
* `customer_email` (nullable)
* `customer_phone` (nullable)
* `return_url` (nullable) – for redirect after payment
* `callback_url` (nullable) – override; generally PaymentChannel’s webhook will be global
* `metadata` (json, nullable) – free extra data (module/context)
* `expires_at` (nullable)
* timestamps

---

### 3.4 `PayTransaction`

**Table:** `pay_transactions`

Represents actual gateway transaction events (authorize, capture, refund).

Fields:

* `id`
* `pay_intent_id`
* `company_id`
* `provider`
* `transaction_type` (enum: `payment`, `refund`, `fee`, `payout`)
* `amount` (decimal)
* `currency` (string)
* `provider_transaction_id` (string)
* `status` (enum: `pending`, `successful`, `failed`)
* `raw_payload` (json) – gateway response or webhook payload
* `processed_at` (nullable)
* `error_message` (nullable)
* timestamps

A single `PayIntent` may have multiple `PayTransaction`s (e.g. partial payments, refunds, retries).

---

## 4. Trait for Payable Models

### 4.1 `HasPayments` Trait

Any model that can be paid for should use `HasPayments`.

```php
trait HasPayments
{
    public function payIntents()
    {
        return $this->morphMany(PayIntent::class, 'payable');
    }

    public function getPaymentDescription(): ?string
    {
        // E.g. "Hostel Booking #REF" or "Invoice #INV-0001"
        return $this->payment_description ?? (string) $this->id;
    }

    public function getPaymentAmount(): float
    {
        // Return the amount to be paid (or outstanding balance)
        return $this->amount ?? $this->total ?? 0;
    }

    public function getPaymentCurrency(): string
    {
        return $this->currency ?? 'GHS';
    }

    public function getPaymentCustomerName(): ?string
    {
        // Try to fetch related tenant/customer name if available
        return $this->customer_name ?? null;
    }

    public function getPaymentCustomerEmail(): ?string
    {
        return $this->customer_email ?? null;
    }

    public function getPaymentCustomerPhone(): ?string
    {
        return $this->customer_phone ?? null;
    }
}
```

Examples of payable models:

* `HostelBooking`
* `FarmSale`
* `ConstructionProjectInvoice`
* `Invoice` in Finance module
* `Subscription` for SaaS billing (later)

---

## 5. Gateway Interface & Implementations

### 5.1 Interface

`PaymentGatewayInterface`:

```php
interface PaymentGatewayInterface
{
    /**
     * Initialize a payment with the gateway.
     * Return data needed for redirect/inline checkout.
     */
    public function initialize(PayIntent $intent): PaymentInitResponse;

    /**
     * Verify the status of a payment with the gateway.
     */
    public function verify(PayIntent $intent, array $payload = []): PaymentVerifyResult;

    /**
     * Handle webhook payload from the provider.
     */
    public function handleWebhook(array $payload): PayIntent;
}
```

`PaymentInitResponse` and `PaymentVerifyResult` can be simple DTOs or arrays:

* `PaymentInitResponse` should include:

    * `redirect_url` (if applicable)
    * `reference`
    * `provider_reference`
    * any JS data for inline flows

* `PaymentVerifyResult` should include:

    * final `status`
    * `amount`, `currency`
    * `provider_transaction_id`
    * raw payload

### 5.2 Implementations

* `FlutterwaveGateway`
* `PaystackGateway`
* `PaySwitchGateway`
* `StripeGateway`
* `GhanaPayGateway`
* `ManualGateway` (for manual/offline payments, just logs and marks as successful after admin confirmation)

Each gateway:

* Reads config from `PayProviderConfig->config`.
* Maps PaymentChannel reference to provider-specific reference.
* Implements `initialize`, `verify`, `handleWebhook`.

---

## 6. PaymentService (Core API)

Located at:
`Modules/PaymentChannel/Services/PaymentService.php`

### 6.1 API Methods

#### 1) Create Intent for a model

```php
Payment::createIntentForModel(
    payable: $model,
    provider: 'flutterwave' | 'paystack' | 'payswitch' | 'stripe' | 'ghanapay',
    options: [
        'method_code' => 'card',       // or 'momo', 'ghanapay', etc.
        'amount'      => 123.45,      // optional override
        'currency'    => 'GHS',       // optional override
        'return_url'  => 'https://kharis.com/payment/return',
        'metadata'    => [...],
    ]
): PayIntent;
```

This should:

* Derive amount/currency/customer from the model if not given.
* Create `PayIntent` with `status = pending`.
* Use default provider if `provider` is not explicitly set.

#### 2) Initialize payment (get redirect URL / inline data)

```php
Payment::initialize(PayIntent $intent): PaymentInitResponse;
```

This:

* Resolves gateway class from `provider`.
* Calls `initialize()`.
* Updates `PayIntent` with `status = initiated`, `provider_reference`.
* Returns redirect URL or inline data (for Livewire UI to use).

#### 3) Verify payment (after redirect or manual check)

```php
Payment::verify(PayIntent $intent, array $payload = []): PaymentVerifyResult;
```

This:

* Calls gateway `verify`.
* Updates `PayIntent` status (`successful` / `failed`).
* Creates `PayTransaction`.
* Fires domain events (`PaymentSucceeded`, `PaymentFailed`).

---

## 7. Webhooks

### 7.1 Routes

`Modules/PaymentChannel/Routes/api.php`:

Example:

```php
Route::prefix('payment/webhooks')
    ->middleware('api')
    ->group(function () {
        Route::post('flutterwave', [WebhookController::class, 'flutterwave'])->name('payment.webhook.flutterwave');
        Route::post('paystack', [WebhookController::class, 'paystack'])->name('payment.webhook.paystack');
        Route::post('payswitch', [WebhookController::class, 'payswitch'])->name('payment.webhook.payswitch');
        Route::post('stripe', [WebhookController::class, 'stripe'])->name('payment.webhook.stripe');
        Route::post('ghanapay', [WebhookController::class, 'ghanapay'])->name('payment.webhook.ghanapay');
    });
```

Each action:

* Validates signature/security according to provider.
* Passes payload to `PaymentService` / gateway `handleWebhook`.
* Updates `PayIntent` and `PayTransaction`.
* Fires events (`PaymentSucceeded`, `PaymentFailed`).

No business module should define its own webhook routes for these gateways.

---

## 8. Integration Workflows with Other Modules

### 8.1 Example: Hostel Booking Payment

1. **Hostels module** (e.g. from a Livewire component):

   ```php
   $intent = Payment::createIntentForModel($booking, provider: 'flutterwave', options: [
       'method_code' => 'card',
       'return_url'  => route('hostels.payment.return', $booking),
   ]);

   $init = Payment::initialize($intent);
   return redirect()->away($init->redirect_url);
   ```

2. **User pays** on Flutterwave.

3. **Flutterwave webhook** hits `payment/webhooks/flutterwave`.

    * PaymentChannel verifies and marks `PayIntent` as `successful` or `failed`.
    * Fires `PaymentSucceeded` event with `$intent`.

4. **Hostels listener** for `PaymentSucceeded`:

    * Marks `booking` as paid.
    * Optionally uses CommunicationCentre to send receipt.

Same pattern works for:

* Finance invoices,
* Farm product sales,
* Construction project payments,
* Manufacturing orders,
* Subscription fees.

---

## 9. Events

PaymentChannel fires generic events:

* `PaymentIntentCreated`
* `PaymentInitialized`
* `PaymentSucceeded`
* `PaymentFailed`
* `PaymentRefunded` (future)

These events carry the `PayIntent` (and the payable model via relation).
Other modules listen and react.

---

## 10. Filament Admin (PaymentChannel)

### 10.1 `PayProviderConfigResource`

Manage gateway configs:

* Provider
* Mode (sandbox/live)
* API keys, secret keys
* Default flag per company
* Enabled/disabled

### 10.2 `PayMethodResource`

Manage user-facing methods:

* Method code (`card`, `momo`, `ghanapay`)
* Display name
* Provider used
* Currency
* Sort order
* Active flag

### 10.3 `PayIntentResource`

Admin view of all payment intents:

* Filters: provider, status, company, creation date
* See payable type/id, amount, customer details
* Link to PayTransaction list

### 10.4 `PayTransactionResource`

Full transaction log:

* provider
* amount, currency
* provider transaction id
* status
* raw payload
* error message (if any)

---

## 11. Module Toggling Rules

`module.json` for PaymentChannel:

* When `"enabled": true`:

    * Register PaymentService binding/facade.
    * Register Filament resources.
    * Register webhook routes.
    * Register events & listeners.

* When `"enabled": false`:

    * Do **not** register routes, resources, events.
    * PaymentService methods should either:

        * Throw a clear exception (“PaymentChannel module is disabled”)
        * Or behave as a stub/no-op in dev environments (your choice).

Other modules should not crash if PaymentChannel is off; they should check availability where necessary.
