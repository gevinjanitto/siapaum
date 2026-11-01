<?php

namespace App\Models;

use App\Models\Kuesioner;
use Illuminate\Support\Str;
use App\Models\GroupLayananSkm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LayananSkm extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ulid = (string) Str::ulid();
        });
    }

    public function kuesioner()
    {
        return $this->hasMany(Kuesioner::class);
    }

    public function groupLayananSkm()
    {
        return $this->belongsTo(GroupLayananSkm::class);
    }
}
