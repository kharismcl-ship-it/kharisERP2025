# SendMessageAction Component

A reusable Filament action component for sending messages through various communication channels.

## 📋 Overview

The `SendMessageAction` provides a standardized way to send messages (email, SMS, WhatsApp, etc.) from any Filament resource. It integrates with the existing CommunicationService and supports template-based messaging.

## 🚀 Quick Start

### Basic Usage

```php
use Modules\CommunicationCentre\Filament\Components\SendMessageAction;

// In your Filament resource table or form
SendMessageAction::make(
    recipientName: $record->full_name,
    recipientContact: $record->email, // or phone number
    defaultChannel: 'email' // optional
)
```

### Example in Employee Resource

```php
// In your Filament resource class
public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ... your columns
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            
            // Add send message action
            SendMessageAction::make(
                recipientName: fn($record) => $record->full_name,
                recipientContact: fn($record) => $record->email,
                defaultChannel: 'email'
            )
        ]);
}
```

## 📝 Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `recipientName` | string | Yes | The name of the message recipient |
| `recipientContact` | string | Yes | Contact information (email, phone number) |
| `defaultChannel` | string | No | Default channel (email, sms, whatsapp, database) |

## 🎯 Features

### Dynamic Channel Selection
- Automatically loads available channels from `config/communicationcentre.php`
- Supports all configured channels (email, SMS, WhatsApp, database)

### Template Integration
- Shows only templates relevant to the selected channel
- Auto-fills subject and message when a template is selected
- Supports template variables (e.g., `{{recipient_name}}`)

### Smart Form Fields
1. **Channel Dropdown** - All available communication channels
2. **Template Selector** - Filtered by selected channel
3. **Subject Field** - Auto-filled from template or manual input
4. **Message Body** - Auto-filled from template or manual composition

## 🔧 Configuration

Ensure your communication channels are configured in:

```php
// config/communicationcentre.php
'channels' => [
    'email',
    'sms', 
    'whatsapp',
    'database',
],
```

## 📋 Usage Examples

### In Table Actions

```php
// In your Filament resource table
Tables\Actions\ActionGroup::make([
    Tables\Actions\ViewAction::make(),
    Tables\Actions\EditAction::make(),
    
    SendMessageAction::make(
        recipientName: fn($record) => $record->name,
        recipientContact: fn($record) => $record->contact_email,
        defaultChannel: 'email'
    )
    ->hidden(fn($record) => !$record->contact_email),
])
```

### In Form Actions

```php
// In your Filament resource form
Forms\Components\Actions::make([
    SendMessageAction::make(
        recipientName: fn($record) => $record->name,
        recipientContact: fn($record) => $record->phone,
        defaultChannel: 'sms'
    )
    ->hidden(fn($record) => !$record->phone),
])
```

### With Different Contact Types

```php
// For email contacts
SendMessageAction::make(
    recipientName: $user->name,
    recipientContact: $user->email,
    defaultChannel: 'email'
)

// For SMS contacts  
SendMessageAction::make(
    recipientName: $customer->name,
    recipientContact: $customer->mobile_phone,
    defaultChannel: 'sms'
)

// For WhatsApp contacts
SendMessageAction::make(
    recipientName: $employee->full_name,
    recipientContact: $employee->whatsapp_number,
    defaultChannel: 'whatsapp'
)
```

## 🎨 Customization

### Custom Button Label and Icon

```php
SendMessageAction::make($name, $contact, $channel)
    ->label('Send Notification')
    ->icon('heroicon-o-bell')
    ->color('warning')
```

### Conditional Visibility

```php
SendMessageAction::make($name, $contact, $channel)
    ->hidden(fn($record) => !$record->has_valid_contact)
    ->visible(fn($record) => $record->is_active)
```

## 🔍 How It Works

1. **User clicks** the Send Message action
2. **Modal opens** with channel selection and form fields
3. **Channel selected** - templates are filtered accordingly
4. **Template selected** - subject and message are auto-filled
5. **Message sent** - uses CommunicationService with proper error handling
6. **Notification shown** - success/error feedback to user

## 📊 Integration Points

- Uses `CommunicationService::send()` for actual message delivery
- Leverages existing `CommTemplate` model for template management
- Follows same patterns as `NotificationPreferenceForm`
- Maintains consistency with other communication components

## 🚨 Error Handling

The component includes comprehensive error handling:

- Validates channel availability
- Handles template loading errors
- Provides user-friendly error messages
- Shows success notifications on successful sends

## 📦 Dependencies

- Filament PHP framework
- CommunicationCentre module
- Configured communication providers
- Existing CommTemplate records (optional)

## 🔄 Version Compatibility

- Filament v3.x
- Laravel 10.x+
- PHP 8.2+

## 🆘 Troubleshooting

### Common Issues

1. **No channels available** - Check `config/communicationcentre.php`
2. **Templates not loading** - Ensure templates exist for the channel
3. **Send failures** - Verify provider configurations in CommProviderConfig

### Debug Mode

Enable debug mode to see detailed error messages:

```php
SendMessageAction::make($name, $contact, $channel)
    ->extraAttributes(['class' => 'debug-mode'])
```

## 📞 Support

For issues with this component, check:
- CommunicationService configuration
- Channel provider setups
- Template availability

---

**File**: `app/Filament/Components/SendMessageAction.php`  
**Module**: CommunicationCentre  
**Author**: System  
**Version**: 1.0.0