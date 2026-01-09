# PaymentsChannel Module

The **PaymentsChannel** module is a universal payment engine for the entire Kharis ERP. It centralizes all online payments from any module (Hostels, Farms, Construction, Manufacturing, HR/Payroll, Finance, etc.) and supports multiple gateways.

## Supported Payment Gateways

- **Flutterwave**
- **Paystack**
- **PaySwitch**
- **Stripe**
- **GhanaPay**
- **Manual** (for offline records)

## Features

- Centralized payment processing
- Multi-gateway support
- Payment intent and transaction tracking
- Webhook handling
- Admin panel for managing providers, methods, and transactions
- Multi-currency support
- Provider configuration per company

## Installation

The module is automatically registered when installed via Composer.

## Usage

### Creating a Payment Intent

```php
use Modules\PaymentsChannel\App\Facades\Payment;

$intent = Payment::createIntentForModel(
    payable: $model,
    provider: 'flutterwave',
    options: [
        'method_code' => 'card',
        'amount'      => 123.45,
        'currency'    => 'GHS',
        'return_url'  => 'https://kharis.com/payment/return',
        'metadata'    => [...],
    ]
);
```

### Initializing a Payment

```php
$init = Payment::initialize($intent);
return redirect()->away($init->redirect_url);
```

### Verifying a Payment

```php
$result = Payment::verify($intent, $payload);
```

## Models

- **PayProviderConfig** - Gateway credentials and settings
- **PayMethod** - Logical payment methods visible to users
- **PayIntent** - Payment attempts/intents
- **PayTransaction** - Actual gateway transaction events

## Traits

- **HasPayments** - Add payment capabilities to any model

## Gateways

- **FlutterwaveGateway**
- **PaystackGateway**
- **PaySwitchGateway**
- **StripeGateway**
- **GhanaPayGateway**
- **ManualGateway** (for manual/offline payments)

## Filament Resources

The module includes Filament admin panel resources for managing:
- Provider configurations
- Payment methods
- Payment intents
- Payment transactions

## Webhooks

The module provides webhook endpoints for all supported payment gateways:
- `/api/payment/webhooks/flutterwave`
- `/api/payment/webhooks/paystack`
- `/api/payment/webhooks/payswitch`
- `/api/payment/webhooks/stripe`
- `/api/payment/webhooks/ghanapay`

These endpoints handle payment status updates from the respective payment providers.