<?php

return [
    'otp' => [
        'label' => env('KWYK_CHECK_OTP_LABEL', 'KWYK_CHECK'),
        'period' => env('KWYK_CHECK_OTP_PERIOD', 10 * 60), //10 minutes
    ],
];
