<?php

namespace App\Models;

use App\Models\AlurPermohonan;
use App\Models\BerkasPermohonan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ValidasiBerkas extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'alur_permohonan_id',
        'berkas_permohonan_id',
        'status',
        'catatan',
    ];

    public function alurPermohonan()
    {
        return $this->belongsTo(AlurPermohonan::class);
    }

    public function berkasPermohonan()
    {
        return $this->belongsTo(BerkasPermohonan::class);
    }
}
