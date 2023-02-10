<?php

namespace App\Enums;

use Illuminate\Support\Arr;

enum ChannelEnum:string {
    case STORAGE = 'Storage';
    case EMAIL = 'Email';
    case SMS = 'SMS';
    case WEB_HOOK = 'Web Hook';

    static function random() {
        return self:: from(Arr::random(array_column(ChannelEnum::cases(),'value')));
    }
}
