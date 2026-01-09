<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Employee Role
    |--------------------------------------------------------------------------
    |
    | This is the default role that will be assigned to employees when their
    | user accounts are automatically created. Set to null if you don't want
    | to assign a default role.
    |
    */
    'default_employee_role' => 'panel_user',

    /*
    |--------------------------------------------------------------------------
    | Auto-create User Accounts
    |--------------------------------------------------------------------------
    |
    | When enabled, user accounts will be automatically created when employees
    | are added to the system, using their email address.
    |
    */
    'auto_create_user_accounts' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Password Length
    |--------------------------------------------------------------------------
    |
    | The length of the randomly generated password for new employee accounts.
    |
    */
    'default_password_length' => 12,
];
