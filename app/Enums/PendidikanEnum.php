<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum PendidikanEnum: string
{
    use EnumToArray;
    case SD = 'SD';
    case SMP = 'SMP';
    case SMA = 'SMA/SMK';
    case DIPLOMA = 'DIPLOMA';
    case S1 = 'S1';
    case S2 = 'S2';
    case LAINNYA = 'Lain-lain';

    public function deskripsi(): string
    {
        return match ($this) {
            self::SD => 'Sekolah Dasar',
            self::SMP => 'Sekolah Menengah Pertama',
            self::SMA => 'Sekolah Menengah Atas/Kejuruan',
            self::DIPLOMA => 'Diploma',
            self::S1 => 'Sarjana',
            self::S2 => 'Magister',
            self::LAINNYA => 'Lain-lain',
        };
    }
}
