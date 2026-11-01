<?php

namespace App\Traits;
trait EnumToArray
{

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function descriptions(): array
    {
        return array_map(fn($value) => $value->deskripsi(), self::cases());
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::descriptions());
    }

    public static function arrayValues(): array
    {
        return array_map(fn($value, $description) => [
            'key' => $value,
            'value' => $description
        ], self::values(), self::descriptions());
    }

    public static function arrayWithout($key)
    {
        $array = self::array();
        unset($array[$key]);
        return $array;
    }
}
