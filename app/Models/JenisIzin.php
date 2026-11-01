<?php

namespace App\Models;

use App\Models\Kuesioner;
use App\Models\Permohonan;
use App\Models\SektorIzin;
use Illuminate\Support\Str;
use App\Models\KategoriIzin;
use App\Models\AlurJenisIzin;
use App\Models\FormJenisIzin;
use App\Models\BerkasJenisIzin;
use App\Models\KelengkapanJenisIzin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisIzin extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'template_surat',
        'is_pas_foto_required',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->ulid)) {
                $model->ulid = (string) Str::ulid();
            }
        });
    }

    public function kategoriIzin()
    {
        return $this->belongsTo(KategoriIzin::class);
    }

    public function sektorIzin()
    {
        return $this->belongsTo(SektorIzin::class);
    }

    public function kuesioner()
    {
        return $this->hasMany(Kuesioner::class);
    }

    public function alurJenisIzin()
    {
        return $this->hasMany(AlurJenisIzin::class);
    }

    public function formJenisIzin()
    {
        return $this->hasMany(FormJenisIzin::class);
    }

    public function berkasJenisIzin()
    {
        return $this->hasMany(BerkasJenisIzin::class);
    }

    public function kelengkapanJenisIzin()
    {
        return $this->hasMany(KelengkapanJenisIzin::class);
    }

    public function permohonan()
    {
        return $this->hasMany(Permohonan::class);
    }
}
