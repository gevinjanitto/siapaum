<?php

namespace App\Enums;
use App\Traits\EnumToArray;

enum JenisPekerjaanEnum: string
{
    use EnumToArray;
    case PNS = 'PNS';
    case TNI = 'TNI';
    case POLRI = 'POLRI';
    case SWASTA = 'SWASTA';
    case WIRASWASTA = 'WIRASWASTA';
    case LAINNYA = 'LAINNYA';

    public function deskripsi(): string
    {
        return match ($this) {
            self::PNS => 'PNS',
            self::TNI => 'TNI',
            self::POLRI => 'POLRI',
            self::SWASTA => 'Karyawan Swasta',
            self::WIRASWASTA => 'Wiraswasta',
            self::LAINNYA => 'Lain-lain',
        };
    }
}
