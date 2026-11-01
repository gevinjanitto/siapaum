<?php

namespace App\Models;

use App\Models\User;
use App\Models\JenisIzin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AlurJenisIzin extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_izin_id',
        'verifikator_id',
        'jenis_verifikator',
        'urutan',
    ];

    public function jenisIzin()
    {
        return $this->belongsTo(JenisIzin::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}
