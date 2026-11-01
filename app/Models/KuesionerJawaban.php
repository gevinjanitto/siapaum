<?php

namespace App\Models;

use App\Models\Kuesioner;
use App\Models\KuesionerOpsi;
use App\Models\KuesionerPertanyaan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KuesionerJawaban extends Model
{
    use HasFactory;

    protected $fillable = [
        'kuesioner_id',
        'kuesioner_opsi_id',
        'kuesioner_pertanyaan_id',
        'pertanyaan',
        'opsi',
        'point',
        'state',
    ];

    public function kuesioner()
    {
        return $this->belongsTo(Kuesioner::class);
    }

    public function kuesionerOpsi()
    {
        return $this->belongsTo(KuesionerOpsi::class);
    }

    public function kuesionerPertanyaan()
    {
        return $this->belongsTo(KuesionerPertanyaan::class);
    }
}
