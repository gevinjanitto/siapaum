<?php

namespace App\Models;

use App\Models\SektorIzin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriIzin extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    public function sektorIzin()
    {
        return $this->hasMany(SektorIzin::class);
    }
}
