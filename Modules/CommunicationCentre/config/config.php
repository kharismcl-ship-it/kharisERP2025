<?php

return [
    'name' => 'CommunicationCentre',
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Communication Channels
    |--------------------------------------------------------------------------
    |
    | List of available communication channels
    |
    */
    'channels' => [
        'email',
        'sms',
        'whatsapp',
        'database',
    ],

    /*
    |--------------------------------------------------------------------------
    | Communication Providers
    |--------------------------------------------------------------------------
    |
    | List of available communication providers
    |
    */
    'providers' => [
        'laravel_mail',
        'mailtrap',
        'twilio',
        'mnotify',
        'wasender',
        'filament_database',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mailtrap Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Mailtrap email service provider
    |
    */
    'mailtrap' => [
        'api_token' => env('MAILTRAP_API_TOKEN'),
        'from_email' => env('MAILTRAP_FROM_EMAIL', 'no-reply@example.com'),
        'from_name' => env('MAILTRAP_FROM_NAME', 'System Notification'),
        'category' => env('MAILTRAP_CATEGORY', 'transactional'),
        'sandbox' => env('MAILTRAP_SANDBOX', true),
        'bulk_template_uuid' => env('MAILTRAP_BULK_TEMPLATE_UUID'),
        'bulk_batch_size' => env('MAILTRAP_BULK_BATCH_SIZE', 100),
        'bulk_timeout' => env('MAILTRAP_BULK_TIMEOUT', 30),
    ],
];
