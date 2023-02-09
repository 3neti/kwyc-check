<?php

namespace App\Helpers;

class DataHelper
{
    public static function users(): array
    {
        return [
            ['id' => 1, 'name' => 'Lester B. Hurtado', 'email' => 'lester@hurtado.ph', 'password' => bcrypt('password')],
        ];
    }

    public static function organizations(): array
    {
        return [
            ['id' => 1, 'name' => 'Default', 'admin_id' => 1],
        ];
    }

    public static function products(): array
    {
        return [
            ['code' => 'ABC', 'name' => 'Product ABC', 'price' => 1000000],
            ['code' => 'DEF', 'name' => 'Product DEF', 'price' => 2000000],
            ['code' => 'GHI', 'name' => 'Product GHI', 'price' => 3000000],
        ];
    }

    public static function packages(): array
    {
        return [
            ['code' => 'ABC', 'name' => 'Package ABC', 'price' => 1000000],
            ['code' => 'DEF', 'name' => 'Package DEF', 'price' => 2000000],
            ['code' => 'GHI', 'name' => 'Package GHI', 'price' => 3000000],
        ];
    }
}
