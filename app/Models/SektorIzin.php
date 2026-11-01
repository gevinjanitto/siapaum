<?php

namespace App\Models;

use App\Models\JenisIzin;
use App\Models\KategoriIzin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SektorIzin extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_izin_id',
        'nama'
    ];

    public function kategoriIzin()
    {
        return $this->belongsTo(KategoriIzin::class);
    }

    public function jenisIzin()
    {
        return $this->hasMany(JenisIzin::class);
    }
}
