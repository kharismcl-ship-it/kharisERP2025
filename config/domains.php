<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Domain → Redirect Map
    |--------------------------------------------------------------------------
    |
    | Maps incoming hostnames to a redirect path for the root URL ("/").
    | Any domain listed here will have its homepage redirected to the
    | specified path, allowing vanity domains to serve as branded entry
    | points to specific module portals within the same Laravel app.
    |
    | www. variants are resolved automatically (both map to same path).
    |
    | Format: 'hostname' => '/path'
    |
    | Paths:
    |   /hostels          → Public hostel listing & room booking (no auth)
    |   /hostel-occupant  → Existing occupant portal (hostel_occupant auth)
    |   /farms            → Farm portal (web auth required)
    |   /staff/{slug}     → Not recommended here; use panel login instead
    |
    */

    'redirect_map' => [
        // khariscourt.com → public hostel booking portal
        'khariscourt.com'     => '/hostels',
        'www.khariscourt.com' => '/hostels',

        // alphafarms.org → public farm marketplace (no auth required)
        'alphafarms.org'      => '/farm-shop',
        'www.alphafarms.org'  => '/farm-shop',
    ],

];
