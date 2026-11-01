<?php

namespace App\Models;

use App\Models\Permohonan;
use App\Models\ValidasiBerkas;
use App\Enums\JenisVerifikatorEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AlurPermohonan extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'verifikator_id',
        'jenis_verifikator',
        'urutan',
        'is_done',
    ];

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }

    public function getJenisVerifikatorNameAttribute()
    {
        return JenisVerifikatorEnum::from($this->jenis_verifikator)->deskripsi();
    }

    public function validasiBerkas()
    {
        return $this->hasMany(ValidasiBerkas::class);
    }

    public function validasiForm()
    {
        return $this->hasMany(ValidasiForm::class);
    }
}
