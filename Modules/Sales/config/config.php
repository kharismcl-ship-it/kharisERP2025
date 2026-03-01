<?php

return [
    'name' => 'Sales',

    'default_tax_rate'          => 15.0,
    'quotation_validity_days'   => 30,
    'default_currency'          => 'GHS',

    'references' => [
        'quotation_prefix' => 'QUO',
        'order_prefix'     => 'SO',
        'pos_prefix'       => 'POS',
    ],
];