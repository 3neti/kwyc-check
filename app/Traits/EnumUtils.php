<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait EnumUtils
{
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
}
