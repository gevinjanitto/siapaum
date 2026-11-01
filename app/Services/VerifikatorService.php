<?php

namespace App\Services;

use App\Models\User;
use App\Models\Permohonan;
use App\Models\AlurPermohonan;
use App\Enums\JenisVerifikatorEnum;
use App\Enums\StatusPermohonanEnum;
use App\Services\PermohonanService;
use App\Exceptions\ServiceException;
use Illuminate\Auth\Authenticatable;

class VerifikatorService
{
    public function construct()
    {
        //
    }

    public function isVerifikatorApprovableBerkas(Permohonan $permohonan, User|Authenticatable $verifikator)
    {
        $permohonan_service = new PermohonanService();
        // isPermohoanVerifiable
        if (!$permohonan_service->isPermohonanCanVerified($permohonan)) {
            return false;
        }
        return $this->isVerifikatorTurn($permohonan, $verifikator) && $this->isJenisVerifikatorApprovable($this->getLastStepAlurPermohonan($permohonan));
    }

    // fungsi untuk cek apakah sudah saatnya verifikator melakukan verifikasi
    public function isVerifikatorTurn(Permohonan $permohonan, User|Authenticatable $verifikator)
    {
        return $this->getLastStepAlurPermohonan($permohonan)?->verifikator_id == $verifikator->id;
    }

    private function getLastStepAlurPermohonan(Permohonan $permohonan)
    {
        $permohonan->relationLoaded('alurPermohonan') || $permohonan->load('alurPermohonan');
        if ($permohonan->alurPermohonan->where('is_done', true)->isEmpty()) {
            $alur_permohonan = $permohonan->alurPermohonan->sortBy('urutan')->first();
        } else {
            $last_urutan_done = $permohonan->alurPermohonan->where('is_done', true)->max('urutan');
            $alur_permohonan = $permohonan->alurPermohonan->where('urutan', $last_urutan_done + 1)->first();
        }

        return $alur_permohonan;
    }

    public function getAlurPermohonanByVerifikator(Permohonan $permohonan, User|Authenticatable $verifikator): AlurPermohonan
    {
        $permohonan->relationLoaded('alurPermohonan') || $permohonan->load('alurPermohonan');
        $a = $permohonan->alurPermohonan->where('verifikator_id', $verifikator->id)->first();
        if (!$a) {
            throw new ServiceException('Anda tidak memiliki akses untuk verifikasi permohonan ini');
        }
        return $a;
    }

    public function isJenisVerifikatorApprovable(AlurPermohonan $alur_permohonan)
    {
        return in_array($alur_permohonan->jenis_verifikator, [
            JenisVerifikatorEnum::FO->value,
            JenisVerifikatorEnum::OPD->value,
            JenisVerifikatorEnum::BO->value,
        ]);
    }

    public function isJenisVerifikatorViewable(AlurPermohonan $alur_permohonan)
    {
        return in_array($alur_permohonan->jenis_verifikator, [
            JenisVerifikatorEnum::JF->value,
            JenisVerifikatorEnum::PENANDATANGAN->value,
        ]);
    }
}
