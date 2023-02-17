<?php

return [
    'otp' => [
        'label' => env('KWYC_CHECK_OTP_LABEL', 'KWYK_CHECK'),
        'period' => env('KWYC_CHECK_OTP_PERIOD', 10 * 60), //10 minutes
    ],
    'seed' => [
        'user' => [
            'system' => [
                'name' => env('KWYC_CHECK_SYSTEM_NAME', 'System User'),
                'email' => env('KWYC_CHECK_SYSTEM_EMAIL', '3neti@lyflyn.net'),
                'mobile' => env('KWYC_CHECK_SYSTEM_MOBILE', '+639178251991'),
                'password' => env('KWYC_CHECK_SYSTEM_PASSWORD', 'pàśšwôrd'),
                'password_confirmation' => env('KWYC_CHECK_SYSTEM_PASSWORD', 'pàśšwôrd'),
                'terms' => true
            ],
            'guest' => [
                'name' => env('KWYC_CHECK_GUEST_NAME', 'Guest User'),
                'email' => env('KWYC_CHECK_GUEST_EMAIL', 'guest@lyflyn.net'),
                'mobile' => env('KWYC_CHECK_SYSTEM_MOBILE', '+639189362340'),
                'password' => env('KWYC_CHECK_SYSTEM_PASSWORD', 'pàśšwôrd'),
                'password_confirmation' => env('KWYC_CHECK_SYSTEM_PASSWORD', 'pàśšwôrd'),
                'terms' => true
            ],
        ],
    ],

];
