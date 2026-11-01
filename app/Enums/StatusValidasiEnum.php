<?php

namespace App\Enums;

enum StatusValidasiEnum: string
{
    case PENDING = 'pending';
    case REVISI = 'revisi';
    case VALID = 'valid';

    public function deskripsi(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::REVISI => 'Revisi',
            self::VALID => 'Valid',
        };
    }
}
