<?php

namespace App\Models;

use App\Models\LayananSkm;
use App\Models\KuesionerJawaban;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kuesioner extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'layanan_skm_id',
        'jenis_izin_id',
        'jenis_kelamin',
        'pendidikan',
        'pekerjaan',
        'jenis_layanan',
        'is_from_skm',
        'nama',
        'nomor_telepon',
        'email',
    ];

    public function kuesionerJawaban()
    {
        return $this->hasMany(KuesionerJawaban::class);
    }

    public function layananSkm()
    {
        return $this->belongsTo(LayananSkm::class);
    }

    public function jenisIzin()
    {
        return $this->belongsTo(JenisIzin::class);
    }
}
