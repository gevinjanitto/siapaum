<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormJenisIzin extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_izin_id',
        'label',
        'tipe',
        'kode_isian',
        'urutan',
    ];
}
