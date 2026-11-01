<?php

namespace App\Models;

use App\Models\Reklame;
use App\Models\RegistrasiReklame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormReklame extends Model
{
    use HasFactory;

    protected $fillable = [
        'reklame_id',
        'label',
        'tipe',
        'kode_isian',
        'value',
        'urutan',
    ];

    public function Reklame()
    {
        return $this->belongsTo(Reklame::class);
    }
}
