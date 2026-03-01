# CommunicationCentre Module - Integration Guide

## 📋 Overview

The CommunicationCentre module provides a unified communication system for sending messages across multiple channels (email, SMS, WhatsApp) with template support, retry logic, and comprehensive error handling.

## 🚀 Quick Start

### 1. Model Preparation

Your models must implement the following methods:

```php
// Add to your User/Employee models
public function getCommName(): string
{
    return $this->full_name ?? $this->name;
}

public function getCommEmail(): ?string
{
    return $this->email;
}

public function getCommPhone(): ?string
{
    return $this->phone;
}
```

### 2. Basic Usage

```php
use Modules\CommunicationCentre\Services\CommunicationService;

// Send a message using a template
$communicationService = app(CommunicationService::class);

$message = $communicationService->sendToModel(
    $employee,                    // Model instance
    'email',                      // Channel: email, sms, whatsapp
    'leave_request_approved',     // Template code
    [                             // Template data
        'days' => 5, 
        'approver' => auth()->user()->name,
        'start_date' => '2024-01-15'
    ]
);
```

### 3. Alternative Methods

```php
// Send to contact directly
$message = $communicationService->sendToContact(
    'sms',
    null,                        // email (optional)
    '+1234567890',               // phone
    'Leave Approved',            // subject
    'leave_request_approved',    // template code
    ['days' => 5, 'approver' => 'Manager']
);

// Send raw message
$message = $communicationService->sendRaw(
    'whatsapp',
    '+1234567890',
    'Leave Notification',
    'Your leave request has been approved for 5 days.'
);
```

## 📊 Available Channels & Providers

| Channel | Providers | Status |
|---------|-----------|---------|
| Email | Laravel Mail | ✅ Complete |
| SMS | Mnotify, Twilio | ✅ Complete |
| WhatsApp | Twilio, Wasender | ✅ Complete |

## 🎯 Template System

### Template Discovery API

```bash
# Get all available templates
GET /api/v1/templates

# Get template by code
GET /api/v1/templates/leave_request_approved
```

### Template Variables

Use `{{variable}}` syntax in your templates:

```php
// Template subject: Leave Request Approved
// Template body: 
// Hello {{name}}, your leave request for {{days}} days starting {{start_date}} has been approved by {{approver}}.

$data = [
    'name' => $employee->name,
    'days' => 5,
    'start_date' => '2024-01-15',
    'approver' => 'Manager Name'
];
```

### Creating Templates

Use the Filament interface at `/admin/communication/templates` to:
- Create new templates
- Set company-specific templates
- Configure providers
- Manage message preferences

## 🔧 Configuration

### Provider Configuration

Each provider requires specific configuration:

#### Twilio (SMS/WhatsApp)
```php
[
    'account_sid' => 'your_account_sid',
    'auth_token' => 'your_auth_token',
    'from_number' => '+1234567890'
]
```

#### Wasender (WhatsApp)
```php
[
    'base_url' => 'https://api.wasender.com',
    'token' => 'your_api_token',
    'device_id' => 'your_device_id'
]
```

#### Mnotify (SMS)
```php
[
    'api_key' => 'your_api_key',
    'sender_id' => 'your_sender_id'
]
```

## 🔄 Retry Logic & Error Handling

### Automatic Retry System

- **Max Retries**: 3 attempts
- **Backoff Strategy**: Exponential (1m, 2m, 4m)
- **Failure States**: `queued` → `retrying` → `failed_permanently`

### Error Handling

```php
try {
    $message = $communicationService->sendToModel($employee, 'email', 'template_code', $data);
} catch (\Exception $e) {
    // Handle specific communication errors
    Log::error('Communication failed: ' . $e->getMessage());
    
    // Optional: Fallback to different channel
    $message = $communicationService->sendToModel($employee, 'sms', 'template_code', $data);
}
```

### Circuit Breaker Pattern

The system includes automatic circuit breaking:
- After 5 failures, provider is disabled for 5 minutes
- Automatic recovery after timeout
- Manual reset available

## 📊 Monitoring & Logging

### Log Locations

```php
// Message lifecycle logs
Log::channel('communication')->info('Message sent', ['message_id' => $message->id]);

// Error logs
Log::error('Communication failure', [
    'message_id' => $message->id,
    'provider' => 'twilio',
    'error' => $e->getMessage()
]);

// Circuit breaker logs
Log::warning('Provider disabled', ['provider' => 'twilio', 'timeout' => 300]);
```

### Status Tracking

Messages have the following statuses:
- `queued` - Waiting to be sent
- `sending` - Currently being sent
- `sent` - Successfully delivered
- `retrying` - Scheduled for retry
- `failed` - Temporary failure
- `failed_permanently` - Permanent failure after retries

## 🧪 Testing

### Unit Testing

```php
// Mock the communication service
$communicationService = $this->mock(CommunicationService::class);
$communicationService->shouldReceive('sendToModel')
    ->once()
    ->with($employee, 'email', 'leave_request_approved', Mockery::any())
    ->andReturn($mockMessage);

// Test your code that uses communication
$result = $yourService->approveLeave($leaveRequest);
$this->assertTrue($result);
```

### Integration Testing

```php
// Test actual communication flow
$message = $communicationService->sendToModel(
    $employee, 
    'email', 
    'test_template', 
    ['test' => 'data']
);

$this->assertEquals('sent', $message->status);
$this->assertEquals($employee->email, $message->to_email);
```

## 🔌 Advanced Integration

### Custom Providers

Create custom channel providers by implementing `ChannelProviderInterface`:

```php
namespace Modules\CommunicationCentre\Services\ChannelProviders;

use Modules\CommunicationCentre\Models\CommMessage;

class CustomProvider implements ChannelProviderInterface
{
    public function send(CommMessage $message): void
    {
        // Your custom implementation
    }
}
```

### Event Listeners

Listen to communication events:

```php
// In your EventServiceProvider
protected $listen = [
    'Modules\CommunicationCentre\Events\MessageSent' => [
        'App\Listeners\LogMessageSent',
    ],
    'Modules\CommunicationCentre\Events\MessageFailed' => [
        'App\Listeners\HandleMessageFailure',
    ],
];
```

### Queue Integration

For production use, enable queue processing:

```php
// In config/queue.php
'communication' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => 'communication',
    'retry_after' => 90,
],

// Dispatch messages to queue
protected function queueMessage(CommMessage $message): void
{
    dispatch(new SendCommMessageJob($message))->onQueue('communication');
}
```

## 🚨 Troubleshooting

### Common Issues

1. **Template Not Found**
   - Check template code spelling
   - Verify company-specific templates exist
   - Ensure template is active

2. **Provider Configuration Missing**
   - Check provider config in Filament admin
   - Verify API keys and credentials

3. **Message Stuck in Queued**
   - Check queue worker is running
   - Verify database connections

4. **Circuit Breaker Activated**
   - Check provider failure logs
   - Wait for automatic recovery or manually reset

### Debug Mode

Enable debug logging in `.env`:
```bash
COMMUNICATION_DEBUG=true
LOG_CHANNEL=communication
```

## 📈 Performance Considerations

- **Batch Processing**: Use queues for high-volume messaging
- **Caching**: Template and provider configs are cached for performance
- **Rate Limiting**: Implement provider-specific rate limiting
- **Monitoring**: Monitor queue lengths and failure rates

## 🔐 Security

- API keys encrypted at rest
- SSL/TLS for all external communications
- Input validation for template data
- XSS protection in template rendering
- Rate limiting on API endpoints

## 📞 Support

For issues and questions:
1. Check the logs in `storage/logs/communication.log`
2. Verify provider configurations
3. Test with the template discovery API
4. Review circuit breaker status

---

**Version**: 1.0.0  
**Last Updated**: 2024-01-10  
**Compatibility**: Laravel 12+, PHP 8.2+