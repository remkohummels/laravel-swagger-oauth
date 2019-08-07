<?php

return [
    /*
     |--------------------------------------------------------------------------
     | 2FA Company Name
     |--------------------------------------------------------------------------
     |
     | This value is the name of your application. This value is used when the
     | qr code is scanned in mobile.
     |
     */

    '2fa_company_name' => env('COFFECTIVE_2FA_COMPANY_NAME', 'Coffective API'),

    'registration_type' => env('COFFECTIVE_REGISTRATION_TYPE','pending'),

    'telescope_admin' => env('TELESCOPE_ADMIN', 'super-administrator@coffective.com')
];
