<?php

namespace App\Models;

use App\Models\User;
use App\Models\Permohonan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogTte extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'verifikator_id',
        'code',
        'body',
    ];

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class);
    }
}
