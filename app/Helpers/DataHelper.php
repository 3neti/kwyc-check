<?php

namespace App\Helpers;

class DataHelper
{
    public static function products(): array
    {
        return [
            ['name' => 'Onboarding',            'price' => 550],
            ['name' => 'Face Authentication',   'price' => 500],
            ['name' => 'OTP Authentication',    'price' => 100],
        ];
    }

    public static function packages(): array
    {
        return [
            ['name' => 'Registration',   'price' => 2000],
            ['name' => 'Inspection',     'price' => 2000],
            ['name' => 'Qualification',  'price' => 2000],
            ['name' => 'Redemption',     'price' => 2000],
        ];
    }
}
