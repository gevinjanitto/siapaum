<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasJenisIzin extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_izin_id',
        'nama',
        'is_required',
        'urutan',
    ];
}
