<?php

namespace App\Enums;

enum ChannelEnum:string {
    case STORAGE = 'Storage';
    case EMAIL = 'Email';
    case SMS = 'SMS';
    case WEB_HOOK = 'Web Hook';
}
