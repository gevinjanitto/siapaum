<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum StatusPermohonanEnum: string
{
    use EnumToArray;
    case PENDING = 'pending';
    case PERMOHONAN_BARU = 'permohonan_baru';
    case SELESAI = 'selesai';
    case REVISI = 'revisi';
    case VERIFIKASI_ULANG = 'verifikasi_ulang';
    case EXPIRED = 'expired';
    case VERIFIKASI = 'verifikasi';

    public function deskripsi(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PERMOHONAN_BARU => 'Permohonan Baru',
            self::SELESAI => 'Selesai',
            self::REVISI => 'Revisi',
            self::VERIFIKASI_ULANG => 'Verifikasi Ulang',
            self::EXPIRED => 'Expired',
            self::VERIFIKASI => 'Verifikasi',
        };
    }
}
