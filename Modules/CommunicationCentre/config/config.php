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
        'twilio',
        'mnotify',
        'wasender',
    ],
];
