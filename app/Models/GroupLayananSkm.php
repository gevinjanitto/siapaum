<?php

namespace App\Models;

use App\Models\LayananSkm;
use Illuminate\Support\Str;
use App\Models\KuesionerPertanyaan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupLayananSkm extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'image_filepath',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ulid = (string) Str::ulid();
        });
    }

    public function layananSkm()
    {
        return $this->hasMany(LayananSkm::class);
    }
    public function kuesionerPertanyaan()
    {
        return $this->hasMany(KuesionerPertanyaan::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image_filepath ? asset(Storage::url($this->image_filepath)) : null;
    }

}
