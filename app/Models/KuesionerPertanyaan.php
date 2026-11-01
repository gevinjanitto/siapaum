<?php

namespace App\Models;

use App\Models\KuesionerOpsi;
use App\Models\GroupLayananSkm;
use App\Models\KuesionerJawaban;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KuesionerPertanyaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pertanyaan',
        'group_layanan_skm_id',
        'state',
    ];

    public function kuesionerOpsi()
    {
        return $this->hasMany(KuesionerOpsi::class);
    }

    public function groupLayananSkm()
    {
        return $this->belongsTo(GroupLayananSkm::class);
    }

    public function kuesionerJawaban()
    {
        return $this->hasMany(KuesionerJawaban::class);
    }
}
