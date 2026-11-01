<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum RoleEnum: string
{

    use EnumToArray;

    case ADMIN = '1';
    case PUBLIC = '3';
    case VERIFIKATOR = '2';
    case AUDITOR = '4';
    case INPUTER = '5';

    public function deskripsi(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::PUBLIC => 'Public',
            self::VERIFIKATOR => 'Verifikator',
            self::AUDITOR => 'Auditor',
            self::INPUTER => 'Inputer',
        };
    }
}
