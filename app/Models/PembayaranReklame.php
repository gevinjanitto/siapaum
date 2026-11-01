<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranReklame extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'nomor_skpd',
        'is_lunas',
        'skpd_filepath',
        'nominal',
    ];
}
