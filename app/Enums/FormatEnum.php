<?php

namespace App\Enums;

use Illuminate\Support\Arr;

enum FormatEnum:string {
    case TXT = 'Text';
    case CSV = 'CSV';
    case XLS = 'Excel';
    case SQL = 'SQL';

    static function random() {
        return self:: from(Arr::random(array_column(FormatEnum::cases(),'value')));
    }
}
