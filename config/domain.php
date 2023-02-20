<?php

return [
    'otp' => [
        'label' => env('KWYC_CHECK_OTP_LABEL', 'KWYK_CHECK'),
        'period' => env('KWYC_CHECK_OTP_PERIOD', 10 * 60), //10 minutes
    ],
    'seed' => [
        'user' => [
            'system' => [
                'name' => env('KWYC_CHECK_SYSTEM_NAME',  'eyJpdiI6IkdZM1VWYlJEaEFLRjVSQXJjZStZNkE9PSIsInZhbHVlIjoiYlUvK2E5Mk1HSEMrZTBraWNvdWk4SU5Fb2xnbVdvcXdnckE5cFlIUHRhdz0iLCJtYWMiOiI5Mzc3MTk2YzRhYzJhZDgwYTlmMjcyN2M4Y2E0YTUyY2YwMTFhYmQ1OTQ2OTk1OWJjYmIwNDljZjYwZGM5MmM2IiwidGFnIjoiIn0='),
                'email' => env('KWYC_CHECK_SYSTEM_EMAIL', 'eyJpdiI6Imcxd2xpTVY2dDEvWlAvSGtQR1hjVVE9PSIsInZhbHVlIjoibDZtZVppU3Z1WVhnS2V5blB0SGtYSlc2NWwxU2hoWjAxMjZONmVDa29xcz0iLCJtYWMiOiI4YjNlNzE0NDU2YTI0NjU2YzQ3OGUzMmU4MDRlOTZhYWVlZjMxZDBiYTY2NGRmMzE2ODFhMWJkOTRlNjljMDRlIiwidGFnIjoiIn0='),
                'mobile' => env('KWYC_CHECK_SYSTEM_MOBILE', 'eyJpdiI6Im91RE5VVnFOWS93OFhUUUZhMGorZ0E9PSIsInZhbHVlIjoiVG9aNE1ieVpDdzdGeGlxRWxxZXNHempNajFOVWJBS0taK002aHFNTlAzWT0iLCJtYWMiOiIzNzFkZmE5MjBlN2Y3MzRiNDUyNzhiMGZlNzQ0NzcyMGIyZGI3ZmQ4YjVjOWY0ODhiYjUwYWM1YjYzYTg3ZGM5IiwidGFnIjoiIn0='),
                'password' => env('KWYC_CHECK_SYSTEM_PASSWORD', 'eyJpdiI6IkJrSllDOTdyN2FyL1NIdUtwM3k5bnc9PSIsInZhbHVlIjoieU5aWGNrZ0k1bW9zMCtRY3VjV0pOSnBaRGV4RlBvVEp1VHBlaVJUbzV1Zz0iLCJtYWMiOiIxNjBiOTMyNTVlM2YzM2UxZGYzOTUyMTljOWI0ODE2Yzg5ODNjNzZhZGMwYzcwMjU1MTJiMjk4ODQ3YjM1ZTIyIiwidGFnIjoiIn0='),
                'password_confirmation' => env('KWYC_CHECK_SYSTEM_PASSWORD', 'eyJpdiI6IkJrSllDOTdyN2FyL1NIdUtwM3k5bnc9PSIsInZhbHVlIjoieU5aWGNrZ0k1bW9zMCtRY3VjV0pOSnBaRGV4RlBvVEp1VHBlaVJUbzV1Zz0iLCJtYWMiOiIxNjBiOTMyNTVlM2YzM2UxZGYzOTUyMTljOWI0ODE2Yzg5ODNjNzZhZGMwYzcwMjU1MTJiMjk4ODQ3YjM1ZTIyIiwidGFnIjoiIn0='),
                'terms' => 'eyJpdiI6InpUdVg3RlJXdmkwWEtMeUxVcllDOVE9PSIsInZhbHVlIjoiRVJkeExRZkVvRGRFcGRTa1R1RkhnUT09IiwibWFjIjoiMWZhOTAzZjA3MzA0MDM5MGU3MGJhODMyNjkzMDVkZTc4YjFlNWRmMTFhMzVjNGNiMjdkNzBiMWU2MTJkZjY1MCIsInRhZyI6IiJ9'
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
    'default' => [
        'user' => [
            'attribs' => [
                'password' => '$2y$10$DMfjP1twBt74819QVtQU/uXaPJa.Ac.dUEsIW/QjBw.Ex8xEZ3V0G',
                'password_confirmation' => '$2y$10$DMfjP1twBt74819QVtQU/uXaPJa.Ac.dUEsIW/QjBw.Ex8xEZ3V0G',
                'terms' => true,
            ],
        ],
    ],

];
