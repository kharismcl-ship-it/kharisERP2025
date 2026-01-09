# CommunicationCentre Module

The **CommunicationCentre** module is a cross-cutting, universal communication engine for the entire Kharis ERP. It provides unified sending of:

* **Email**
* **SMS (mNotify)**
* **WhatsApp (Twilio WhatsApp + Wasender WhatsApp)**
* **Any future messaging providers**

## Features

- Unified communication API
- Template-based messaging
- Provider configuration management
- Message logging and tracking
- Communication preferences
- Queue-based message sending
- Event-driven integration with other modules

## Installation

The module is automatically registered when installed via Composer.

## Usage

### Sending Messages to Models

```php
use Modules\CommunicationCentre\App\Facades\Communication;

Communication::sendToModel(
    notifiable: $model,
    channel: 'sms',
    templateCode: 'booking_confirmation',
    data: [...]
);
```

### Sending Messages to Contacts

```php
Communication::sendToContact(
    channel: 'email',
    toEmail: 'user@example.com',
    subject: 'Welcome!',
    templateCode: 'welcome_email',
    data: [...]
);
```

### Sending Raw Messages

```php
Communication::sendRaw(
    channel: 'whatsapp',
    toPhone: '+1234567890',
    subject: 'Hello',
    body: 'This is a test message'
);
```

## Models

- **CommProviderConfig** - Provider credentials & settings
- **CommTemplate** - Reusable communication templates
- **CommMessage** - Communication logs
- **CommPreference** - User communication preferences

## Traits

- **HasCommunicationProfile** - Add communication capabilities to any model

## Providers

- **LaravelMailProvider** - Laravel's built-in mail system
- **TwilioWhatsAppProvider** - Twilio WhatsApp API
- **MnotifySmsProvider** - mNotify SMS service
- **WasenderProvider** - Wasender WhatsApp service

## Filament Resources

The module includes Filament admin panel resources for managing:
- Provider configurations
- Communication templates
- Message logs
- Communication preferences