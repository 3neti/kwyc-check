<?php

return [
    'otp' => [
        'label' => env('KWYC_CHECK_OTP_LABEL', 'KWYK_CHECK'),
        'period' => env('KWYC_CHECK_OTP_PERIOD', 10 * 60), //10 minutes
    ],
    'seed' => [
        'user' => [
            'system' => [
                'name' => env('KWYC_CHECK_SYSTEM_NAME',  'Lester B. Hurtado'),
                'email' => env('KWYC_CHECK_SYSTEM_EMAIL',  '3neti@lyflyn.net'),
                'mobile' => env('KWYC_CHECK_SYSTEM_MOBILE', '+639178251991'),
                'password' => env('KWYC_CHECK_SYSTEM_PASSWORD', '#Password1'),
                'password_confirmation' => env('KWYC_CHECK_SYSTEM_PASSWORD', '#Password1'),
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
        'organization' => [
            'name' => env('KWYC_CHECK_ORGANIZATION_NAME',  '3neti Research & Development OPC'),
            'channel' => \App\Enums\ChannelEnum::default(),
            'format' => \App\Enums\FormatEnum::default(),
            'address' => env('KWYC_CHECK_ADDRESS',  'https://3neti.com/web_hooks'),
            'command' => env('KWYC_CHECK_COMMAND',  'command'),
            'package' => env('KWYC_CHECK_PACKAGE',  'registration'),
        ],
    ],
    'default' => [
        'user' => [
            'attribs' => [
                'password' => env('KWYC_CHECK_USER_PASSWORD', '#Password1'),
            ],
        ],
    ],
    'hyperverge' => [
        'api' => [
            'id' => env('HYPERVERGE_API_ID'),
            'key' => env('HYPERVERGE_API_KEY'),
            'expiry' => env('HYPERVERGE_API_EXPIRY', 300),
            'url' => [
                'login' => env('HYPERVERGE_API_URL_LOGIN', 'https://auth.hyperverge.co/login'),
                'kyc' => env('HYPERVERGE_API_URL_KYC', 'https://ind.idv.hyperverge.co/v1/link-kyc/start'),
                'result' => env('HYPERVERGE_API_URL_RESULT', 'https://ind.idv.hyperverge.co/v1/link-kyc/results'),
            ],
        ],
        'url' => [
            'workflow' => env('HYPERVERGE_URL_WORKFLOW', 'default'),
            'redirect' => env('HYPERVERGE_URL_REDIRECT', 'http://localhost'),
        ],
    ],
];
