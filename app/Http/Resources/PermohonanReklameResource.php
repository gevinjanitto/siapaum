<?php

namespace App\Http\Resources;

use App\Enums\StatusPermohonanEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PermohonanReklameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // status izin logic. If current date is greater than tanggal_akhir_izin, status_izin is "Expired", else "Active"
        if ($this->permohonan) {
            if ($this->permohonan->status == 'pending') {
                $statusIzin = 'Belum Izin';
            } else if ($this->permohonan->status == 'selesai') {
                $tanggalAkhirIzin = $this->permohonan?->formPermohonan?->where('kode_isian', 'TGL_AKHIR')->first()?->value;
                if ($tanggalAkhirIzin) {
                    $currentDate = Carbon::now();
                    $endDate = Carbon::parse($tanggalAkhirIzin);
                    $statusIzin = $currentDate->greaterThan($endDate) ? 'Kadaluarsa' : 'Aktif';
                } else {
                    $statusIzin = 'Belum Izin';
                }
            } else {
                $statusIzin = StatusPermohonanEnum::from($this->permohonan->status)->deskripsi();
            }
        } else {
            $statusIzin = 'Belum Izin';
        }
        return [
            'id' => $this->id,
            'permohonan_id' => $this->permohonan_id,
            'nomor_registrasi' => $this->registrasiReklame?->nomor_registrasi ?? $this->permohonan?->nomor_registrasi,
            'jenis_reklame' => $this->permohonan?->formPermohonan?->where('kode_isian', 'JENIS_REKLAME')->first()?->value,
            'tanggal_akhir_izin' => $this->permohonan?->formPermohonan?->where('kode_isian', 'TGL_AKHIR')->first()?->value ? Carbon::parse($this->permohonan?->formPermohonan?->where('kode_isian', 'TGL_AKHIR')->first()?->value)->format('Y-m-d') : null,
            'tempat_pemasangan' => $this->permohonan?->formPermohonan?->where('kode_isian', 'LOKASI')->first()?->value,
            'nama_wajib_pajak' => $this->permohonan?->formPermohonan?->where('kode_isian', 'NAMA_PERUSAHAAN')->first()?->value,
            'is_bongkar' => $this->is_bongkar,
            'status_izin' => $statusIzin,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
