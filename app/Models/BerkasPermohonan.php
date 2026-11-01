<?php

namespace App\Models;

use App\Models\Permohonan;
use App\Models\ValidasiBerkas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BerkasPermohonan extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'permohonan_id',
        'nama',
        'is_required',
        'urutan',
        'filepath',
        'is_revisi',
        'catatan',
    ];

    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }

    public function validasiBerkas()
    {
        return $this->hasMany(ValidasiBerkas::class);
    }

    public function validasiBerkasIncludeDeleted()
    {
        return $this->hasMany(ValidasiBerkas::class)->withTrashed();
    }
}
