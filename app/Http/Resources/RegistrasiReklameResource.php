<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrasiReklameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->relationLoaded('reklame')) {
            $formReklame = $this->reklame->formReklame->map(function ($item) {
                return [
                    'id' => $item->id,
                    'label' => $item->label,
                    'tipe' => $item->tipe,
                    'kode_isian' => $item->kode_isian,
                    'value' => $item->value,
                    'urutan' => $item->urutan,
                ];
            });
            return [
                'id' => $this->id,
                'nomor_registrasi' => $this->nomor_registrasi,
                'nama' => $this->nama,
                'nik' => $this->nik,
                'npwp' => $this->npwp,
                'nama_perusahaan' => $this->nama_perusahaan,
                'alamat_perusahaan' => $this->alamat_perusahaan,
                'nomor_telepon' => $this->nomor_telepon,
                'form_reklame' => $formReklame,
            ];
        } else {
            return [
                'id' => $this->id,
                'nomor_registrasi' => $this->nomor_registrasi,
                'nama' => $this->nama,
                'nik' => $this->nik,
                'npwp' => $this->npwp,
                'nama_perusahaan' => $this->nama_perusahaan,
                'alamat_perusahaan' => $this->alamat_perusahaan,
                'nomor_telepon' => $this->nomor_telepon,
            ];
        }
    }
}
