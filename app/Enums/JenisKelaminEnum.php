<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum JenisKelaminEnum: int
{
    use EnumToArray;
    case LAKI_LAKI = 1;
    case PEREMPUAN = 0;

    public function deskripsi(): string
    {
        return match ($this) {
            self::LAKI_LAKI => 'Laki-laki',
            self::PEREMPUAN => 'Perempuan',
            default => 'Tidak diketahui',
        };
    }
}
