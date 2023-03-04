<?php

namespace App\Enums;

use App\Traits\EnumUtils;

enum ChannelEnum:string {
    use EnumUtils;

    case STORAGE = 'Storage';
    case EMAIL = 'Email';
    case SMS = 'SMS';
    case WEB_HOOK = 'Web Hook';

    static function default(): self {
        return self::SMS;
    }
}
