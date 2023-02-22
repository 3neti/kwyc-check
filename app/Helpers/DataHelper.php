<?php

namespace App\Helpers;

class DataHelper
{
    public static function products(): array
    {
        return [
            ['code' => 'onboarding', 'name' => 'Onboarding',            'price' => 550],
            ['code' => 'face-auth',  'name' => 'Face Authentication',   'price' => 500],
            ['code' => 'otp-auth',   'name' => 'OTP Authentication',    'price' => 100],
        ];
    }

    public static function packages(): array
    {
        return [
            ['code' => 'registration',  'name' => 'Registration Package',   'price' => 2000],
            ['code' => 'inspection',    'name' => 'Inspection Package',     'price' => 2000],
            ['code' => 'qualification', 'name' => 'Qualification Package',  'price' => 2000],
            ['code' => 'redemption',    'name' => 'Redemption Package',     'price' => 2000],
        ];
    }
}
