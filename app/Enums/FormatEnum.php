<?php

namespace App\Enums;

use App\Traits\EnumUtils;

enum FormatEnum:string {
    use EnumUtils;

    case TXT = 'Text';
    case CSV = 'CSV';
    case XLS = 'Excel';
    case SQL = 'SQL';

    static function default(): self {
        return self::TXT;
    }
}
