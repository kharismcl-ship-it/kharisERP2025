<?php

return [
    'name' => 'PaymentsChannel',
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Payment Providers
    |--------------------------------------------------------------------------
    |
    | List of available payment providers
    |
    */
    'providers' => [
        'flutterwave',
        'paystack',
        'payswitch',
        'stripe',
        'ghanapay',
        'manual',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | List of available payment methods
    |
    */
    'methods' => [
        'card',
        'momo',
        'bank',
        'wallet',
    ],
];
