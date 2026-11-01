<?php

namespace App\Enums;

enum JenisVerifikatorEnum: string
{
    case FO = "0";
    case OPD = "1";
    case BO = "2";
    case JF = "4";
    case PENANDATANGAN = "3";

    public function deskripsi(): string
    {
        return match ($this) {
            self::FO => "Verifikator FO", // upload Surat pengantar permohonan |Surat pengantar permohonan sesuai template tapi input. Harus ttd oleh penandatangan
            self::OPD => "Verifikator OPD", // upload Lampiran Teknis
            self::BO => "Verifikator BO", // upload form kelengkapan verifikator
            self::JF => "Vefikator JF",
            self::PENANDATANGAN => "Penandatangan",
        };
    }
}
