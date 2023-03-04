<?php

namespace App\Enums;

use App\Traits\EnumUtils;

enum PackageEnum:string {
    use EnumUtils;

    case REGISTRATION = 'registration';
    case INSPECTION = 'inspection';
    case QUALIFICATION = 'qualification';
    case REDEMPTION = 'redemption';

    static function default(): self {
        return self::REGISTRATION;
    }
}
