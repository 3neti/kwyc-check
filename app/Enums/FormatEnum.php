<?php

namespace App\Enums;

use Illuminate\Support\Arr;

enum FormatEnum:string {
    case TXT = 'Text';
    case CSV = 'CSV';
    case XLS = 'Excel';
    case SQL = 'SQL';

    static function random(): self {
        return self:: from(Arr::random(array_column(self::cases(),'value')));
    }

    static function values(): array {
        return array_map(
            fn (self $item) => $item->value,
            self::cases()
        );
    }

    static function toArray(): array {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }

        return $array;
    }

    static function toFlippedArray(): array {
        return array_flip(self::toArray());
    }

    static function novaOptions(): array {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = $case->value;
        }

        return $array;
    }

    static function default(): self {
        return self::TXT;
    }
}
