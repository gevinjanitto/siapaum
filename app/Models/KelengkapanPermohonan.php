<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelengkapanPermohonan extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'label',
        'tipe',
        'kode_isian',
        'value',
        'urutan',
    ];
}
