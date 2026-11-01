<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum JenisReklameEnum: string
{
    use EnumToArray;
    case MEGATRON = 'Megatron/Videotron';
    case BILLBOARD = 'Billboard';
    case LED = 'LED';
    case PAPAN = 'Papan';
    case PAPAN_BERCAHAYA = 'Papan Bercahaya';
    case BALIHO = 'Baliho';
    case LAYAR_SPANDUK = 'Layar/Spanduk Umbul-umbul dan Sejenisnya';
    case TEMPEL_PLAT = 'Tempel/Plat/Tembok';
    case SELEBARAN = 'Selebaran';
    case KENDARAAN = 'Kendaraan';


    public function deskripsi(): string
    {
        return match ($this) {
            self::MEGATRON => 'Megatron/Videotron',
            self::BILLBOARD => 'Billboard',
            self::LED => 'LED',
            self::PAPAN => 'Papan',
            self::PAPAN_BERCAHAYA => 'Papan Bercahaya',
            self::BALIHO => 'Baliho',
            self::LAYAR_SPANDUK => 'Layar/Spanduk Umbul-umbul dan Sejenisnya',
            self::TEMPEL_PLAT => 'Tempel/Plat/Tembok',
            self::SELEBARAN => 'Selebaran',
            self::KENDARAAN => 'Kendaraan',
            default => 'Tidak diketahui',
        };
    }
}
