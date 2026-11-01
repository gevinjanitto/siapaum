<?php

namespace App\Enums;
use App\Traits\EnumToArray;

enum AreaPemasanganReklameEnum: string
{
    use EnumToArray;
    case PARIWISATA = 'Pariwisata';
    case PERKOTAAN = 'Perkotaan';
    case LAINNYA = 'Lainnya';

    public function deskripsi(): string
    {
        return match ($this) {
            self::PARIWISATA => 'Pariwisata',
            self::PERKOTAAN => 'Perkotaan',
            self::LAINNYA => 'Lainnya',
            default => 'Tidak diketahui',
        };
    }
}
