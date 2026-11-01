<?php

namespace App\Models;

use App\Models\User;
use App\Models\Reklame;
use App\Models\JenisIzin;
use App\Models\Kuesioner;
use App\Models\AlurPermohonan;
use App\Models\FormPermohonan;
use App\Models\BerkasPermohonan;
use App\Models\PembayaranReklame;
use App\Enums\StatusPermohonanEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permohonan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'jenis_izin_id',
        'nama_jenis_izin',
        'deskripsi_jenis_izin',
        'nomor_registrasi',
        'surat_kuasa_filepath',
        'nama',
        'nik',
        'npwp',
        'tempat_lahir',
        // 'surat_permohonan_rekomendasi_filepath',
        'surat_rekomendasi_filepath',
        'template_surat_filepath',
        'is_ttd',
        'status',
        'is_expired',
        'is_legacy_data',
        'pengajuan_at',
        'is_pas_foto_required',
        'pas_foto_filepath',
        'lampiran_sk_filepath',
    ];

    protected $casts = [
        'pengajuan_at' => 'datetime',
    ];

    public function jenisIzin()
    {
        return $this->belongsTo(JenisIzin::class);
    }

    public function alurPermohonan()
    {
        return $this->hasMany(AlurPermohonan::class)->orderBy('urutan');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formPermohonan()
    {
        return $this->hasMany(FormPermohonan::class)->orderBy('urutan');
    }

    public function berkasPermohonan()
    {
        return $this->hasMany(BerkasPermohonan::class)->orderBy('urutan');
    }

    public function kelengkapanPermohonan()
    {
        return $this->hasMany(KelengkapanPermohonan::class)->orderBy('urutan');
    }

    public function reklame()
    {
        return $this->hasOne(Reklame::class);
    }
    public function pembayaranReklame()
    {
        return $this->hasOne(PembayaranReklame::class);
    }

    public function scopeReklame($query)
    {
        return $query->where('jenis_izin_id', 9);
    }


    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case StatusPermohonanEnum::PENDING->value:
                return 'Pending';
            case StatusPermohonanEnum::PERMOHONAN_BARU->value:
                return 'Permohonan Baru';
            case StatusPermohonanEnum::SELESAI->value:
                return 'Selesai';
            case StatusPermohonanEnum::REVISI->value:
                return 'Revisi';
            case StatusPermohonanEnum::VERIFIKASI_ULANG->value:
                return 'Verifikasi Ulang';
            case StatusPermohonanEnum::EXPIRED->value:
                return 'Expired';
            case StatusPermohonanEnum::VERIFIKASI->value:
                return 'Verifikasi';
            default:
                return 'Undefined';
        }
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->jenis_izin_id == 9) {
            if ($this->reklame?->skpd_filepath && !$this->reklame?->bukti_bayar_filepath && $this->status != StatusPermohonanEnum::SELESAI->value) {
                return '<span class="badge bg-warning">Menunggu Pembayaran</span>';
            }
        }
        switch ($this->status) {
            case StatusPermohonanEnum::PENDING->value:
                return '<span class="badge bg-warning">Draft</span>';
            case StatusPermohonanEnum::PERMOHONAN_BARU->value:
                return '<span class="badge bg-primary">Permohonan Baru</span>';
            case StatusPermohonanEnum::SELESAI->value:
                return '<span class="badge bg-success">Selesai</span>';
            case StatusPermohonanEnum::REVISI->value:
                return '<span class="badge bg-danger">Revisi</span>';
            case StatusPermohonanEnum::VERIFIKASI_ULANG->value:
                return '<span class="badge bg-info">Verifikasi Ulang</span>';
            case StatusPermohonanEnum::EXPIRED->value:
                return '<span class="badge bg-secondary">Expired</span>';
            case StatusPermohonanEnum::VERIFIKASI->value:
                return '<span class="badge bg-info">Verifikasi</span>';
            default:
                return '<span class="badge bg-secondary">Undefined</span>';
        }
    }

    public function getStatusBadgeMergeUsulanBaruToVerifikasiAttribute()
    {
        if ($this->jenis_izin_id == 9) {
            if ($this->reklame?->skpd_filepath && !$this->reklame?->bukti_bayar_filepath && $this->status != StatusPermohonanEnum::SELESAI->value) {
                return '<span class="badge bg-warning">Menunggu Pembayaran</span>';
            }
        }
        switch ($this->status) {
            case StatusPermohonanEnum::PENDING->value:
                return '<span class="badge bg-warning">Draft</span>';
            case StatusPermohonanEnum::PERMOHONAN_BARU->value:
                return '<span class="badge bg-info">Verifikasi</span>';
            case StatusPermohonanEnum::SELESAI->value:
                return '<span class="badge bg-success">Selesai</span>';
            case StatusPermohonanEnum::REVISI->value:
                return '<span class="badge bg-danger">Revisi</span>';
            case StatusPermohonanEnum::VERIFIKASI_ULANG->value:
                return '<span class="badge bg-info">Verifikasi Ulang</span>';
            case StatusPermohonanEnum::EXPIRED->value:
                return '<span class="badge bg-secondary">Expired</span>';
            case StatusPermohonanEnum::VERIFIKASI->value:
                return '<span class="badge bg-info">Verifikasi</span>';
            default:
                return '<span class="badge bg-secondary">Undefined</span>';
        }
    }

    public function getMemohonUntukAttribute()
    {
        return $this->surat_kuasa_filepath ? 'Orang Lain' : 'Diri Sendiri';
    }

    public function getIsMemohonUntukOrangLainAttribute()
    {
        return $this->surat_kuasa_filepath ? 1 : 0;
    }

    public function kuesioner()
    {
        return $this->hasOne(Kuesioner::class);
    }
}
