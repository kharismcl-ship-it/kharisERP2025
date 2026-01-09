## Module Summary

The **CommunicationCentre** module is a **cross-cutting, universal communication engine** for the entire Kharis ERP.
It provides unified sending of:

* **Email**
* **Sendgrid Email**
* **SMS (mNotify)**
* **WhatsApp (Twilio WhatsApp + Wasender WhatsApp)**
* **Any future messaging providers**

Any module (Hostels, Farms, Construction, Manufacturing, HR, Finance, Core) can ask CommunicationCentre to transmit a message to any model that has **email and/or phone number**.

CommunicationCentre handles:

* Provider selection (per company or global)
* Template rendering
* Message logging
* Delivery status tracking
* Retries
* Message preferences
* Queue-based execution

It must be built **as a standalone, toggleable Nwidart module**, cleanly separated from business logic.

---

# 1. Folder Structure

```
Modules/CommunicationCentre/
    Config/
    Database/
        migrations/
    Providers/
        CommunicationCentreServiceProvider.php
        EventServiceProvider.php
    Http/
        Livewire/  (if needed)
    Models/
        CommProviderConfig.php
        CommTemplate.php
        CommMessage.php
        CommPreference.php
    Resources/
        views/
    Services/
        CommunicationService.php
        ChannelProviders/
            ChannelProviderInterface.php
            LaravelMailProvider.php
            TwilioWhatsAppProvider.php
            MnotifySmsProvider.php
            WasenderProvider.php
    Traits/
        HasCommunicationProfile.php
    Filament/
        Resources/
            CommProviderConfigResource.php
            CommTemplateResource.php
            CommMessageResource.php
            CommPreferenceResource.php (optional)
    module.json
    Routes/
        web.php
```

---

# 2. Module Responsibilities

### 2.1 Unified Communication API

Expose a single API usable by any module:

```php
Communication::sendToModel(
    notifiable: $model,
    channel: 'sms' | 'email' | 'whatsapp',
    templateCode: 'booking_confirmation',
    data: [...]
);
```

```php
Communication::sendToContact(
    channel: 'email' | 'sms' | 'whatsapp',
    toEmail?: string,
    toPhone?: string,
    subject?: string,
    templateCode?: string,
    data?: array
);
```

### 2.2 What CommunicationCentre Handles

* Resolving the **company** automatically (from model or current session).
* Loading default provider for the requested channel.
* Rendering template with placeholders.
* Logging the message (`CommMessage`).
* Placing sending into a **queue job**.
* Tracking provider responses.
* Error handling and retry logic.
* Contact-level communication preferences.

### 2.3 What Other Modules Should Not Do

* No module should call Twilio/mNotify/Wasender directly.
* No business module should store API keys or provider configs.
* No module should manually send mail using Mailables for notifications.

All messages flow through **CommunicationCentre** only.

---

# 3. Data Model & Migrations (Full Professional Design)

## 3.1 `CommProviderConfig`

**Table:** `comm_provider_configs`

Purpose: Provider credentials & settings per company and channel.

Fields:

* `id`
* `company_id` (nullable → global default)
* `channel` (`email`, `sms`, `whatsapp`)
* `provider` (`laravel_mail`, `twilio`, `mnotify`, `wasender`)
* `name` (display label)
* `is_default` (boolean)
* `is_active` (boolean)
* `config` (JSON):

    * Twilio: `{ "account_sid": "", "auth_token": "", "from_number": "" }`
    * mNotify: `{ "api_key": "", "sender_id": "" }`
    * Wasender: `{ "base_url": "", "token": "", "device_id": "" }`
* timestamps

Index: `{company_id, channel, provider}`

---

## 3.2 `CommTemplate`

**Table:** `comm_templates`

Purpose: reusable communication templates.

Fields:

* `id`
* `company_id` (nullable, global or per company)
* `code` (unique per company)
* `channel` (`email`, `sms`, `whatsapp`)
* `name`
* `subject` (nullable)
* `body` (longtext) — supports placeholders: `{{name}}`, `{{booking_reference}}`
* `description` (nullable)
* `is_active` (boolean)
* timestamps

---

## 3.3 `CommMessage`

**Table:** `comm_messages`

Purpose: all communication logs.

Fields:

* `id`
* `company_id` (nullable)
* `notifiable_type` (polymorphic)
* `notifiable_id`
* `channel` (`email`, `sms`, `whatsapp`)
* `provider`
* `template_id` (nullable)
* `to_name` (nullable)
* `to_email` (nullable)
* `to_phone` (nullable)
* `subject` (nullable)
* `body` (text)
* `status` (`queued`, `sent`, `delivered`, `failed`)
* `error_message` (nullable)
* `provider_message_id` (nullable)
* `scheduled_at` (nullable)
* `sent_at` (nullable)
* `delivered_at` (nullable)
* timestamps

---

## 3.4 `CommPreference` (Optional but recommended)

**Table:** `comm_preferences`

Fields:

* `id`
* `company_id`
* `notifiable_type`
* `notifiable_id`
* `channel`
* `is_enabled` (boolean)
* timestamps

Used for user/tenant/customer opt-out.

---

# 4. Traits

## 4.1 `HasCommunicationProfile`

Applied to any model that wants communication:

```php
trait HasCommunicationProfile {
    public function commMessages() {
        return $this->morphMany(CommMessage::class, 'notifiable');
    }

    public function getCommName() {
        return $this->name ?? $this->full_name ?? null;
    }

    public function getCommEmail() {
        return $this->email ?? null;
    }

    public function getCommPhone() {
        return $this->phone ?? $this->phone_no ?? $this->mobile ?? null;
    }
}
```

This makes any model communicable, e.g.:

* Hostel Tenant
* Farm Customer
* Employee
* User
* Supplier
* Contractor
* Landlord
* etc.

---

# 5. Channel Providers

All providers implement:

```php
interface ChannelProviderInterface {
    public function send(CommMessage $message): void;
}
```

### Providers:

* `LaravelMailProvider`
* `TwilioWhatsAppProvider`
* `MnotifySmsProvider`
* `WasenderProvider`

Each provider:

* Reads credentials from `CommProviderConfig->config`.
* Performs API call.
* Returns provider message ID or throws error.
* Updates `CommMessage` status.

---

# 6. CommunicationService (Core Engine)

Located at:
`Modules/CommunicationCentre/Services/CommunicationService.php`

Functions:

### 1) Send to model

```php
Communication::sendToModel($model, 'sms', 'booking_confirmation', [...]);
```

### 2) Send to contact

```php
Communication::sendToContact('email', 'user@test.com', null, 'welcome_email', [...]);
```

### 3) Send raw

```php
Communication::sendRaw('whatsapp', toPhone:'+233555...', subject:'', body:'Hello!');
```

### 4) Queue-based sending

All sends **must**:

* Create `CommMessage` with `status = queued`
* Dispatch `SendCommMessageJob`

### 5) Delivery updates

When a provider callback is received (Twilio webhook), the status is updated:

* `sent`
* `delivered`
* `failed`

---

# 7. Workflow Integration with Other Modules

Each module triggers events.
CommunicationCentre listens.

Examples:

### Hostels Module:

* `HostelBookingCreated` → send tenant confirmation
* `HostelBookingPaymentReceived` → send receipt
* `MaintenanceRequestCreated` → notify maintenance officer

### Farms Module:

* `FarmSaleCreated` → send receipt to customer

### Construction Module:

* `ProjectIssueRaised` → notify project manager

### HR Module:

* `EmployeeCreated` → send welcome email
* `LeaveApproved` → notify employee

### Finance Module:

* `InvoiceOverdue` → send payment reminder
* `PaymentPosted` → send receipt

Listeners use:

```php
Communication::sendToModel($tenant, 'sms', 'booking_confirmation', [...]);
```

---

# 8. Filament Resources

## 8.1 CommProviderConfigResource

Manage:

* Providers per channel
* API keys
* “Default provider” toggle
* Enable/disable provider

## 8.2 CommTemplateResource

Manage:

* Template code
* Channel
* Subject
* Body
* Placeholder preview

## 8.3 CommMessageResource

Log viewer:

* Filters: status, provider, channel, date
* View rendered message
* Retry button
* Delivery timeline

## 8.4 CommPreferenceResource (optional)

Manage user/tenant communication opt-outs.

---

# 9. Routes (Optional UI)

`Modules/CommunicationCentre/Routes/web.php`
Optional Livewire UI pages:

* /communication/templates
* /communication/messages

Only loaded if module is enabled.

---

# 10. Module Toggling Rules

`module.json` controls:

* Enabling/disabling providers
* Loading routes
* Loading Filament resources
* Registering events/listeners
* Registering the `Communication` Facade/Binding

If disabled:

* All routes/resources are skipped
* CommunicationService should either:

    * Disable sending cleanly, OR
    * Throw a meaningful exception (“CommunicationCentre is disabled”)

Modules must **never crash** if it’s off.

---

# 11. QA / Testing Expectations

* Test sending with each provider
* Test fallback to default provider
* Test invalid credentials
* Test that logs still record even on error
* Test retry behavior
* Test sending to a model without email or phone
* Test template rendering with missing variables

---

# 12. High-Level Summary for Trae / Junie

> Build a complete, toggleable Nwidart module named **CommunicationCentre** that centralizes all communication (Email, Twilio WhatsApp, Wasender API, mNotify SMS).
> It must expose a universal communication API (`Communication::sendToModel` / `sendToContact`), support template rendering, provider config, message logs, delivery tracking, preferences, queue-based sending, and event-driven integration with other modules like Hostels, Farms, Construction, Manufacturing, HR, and Finance.
> Other modules must never communicate directly with providers; all messaging flows through CommunicationCentre.

---
