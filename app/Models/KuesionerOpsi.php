<?php

namespace App\Models;

use App\Models\KuesionerPertanyaan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KuesionerOpsi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kuesioner_pertanyaan_id',
        'opsi',
        'point',
    ];

    public function kuesionerPertanyaan()
    {
        return $this->belongsTo(KuesionerPertanyaan::class);
    }
}
