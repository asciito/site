<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed emails to access the panel
    |--------------------------------------------------------------------------
    |
    | This option contains the emails allowed to access the panel
    |
    */

    'allowed_emails' => explode(',', env('SITE_ALLOWED_EMAILS', '')) ?? [],
];
