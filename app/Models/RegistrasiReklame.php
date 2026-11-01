<?php

namespace App\Models;

use App\Models\Reklame;
use App\Models\FormReklame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegistrasiReklame extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nomor_registrasi',
        'nama',
        'nik',
        'npwp',
        'nama_perusahaan',
        'alamat_perusahaan',
        'nomor_telepon',
    ];

    public function formReklame()
    {
        return $this->hasManyThrough(FormReklame::class, Reklame::class);
    }

    public function reklame()
    {
        return $this->hasMany(Reklame::class);
    }
}
