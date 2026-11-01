<?php

namespace App\Services;

use App\Enums\StatusValidasiEnum;
use App\Models\AlurPermohonan;
use App\Models\Permohonan;
use App\Models\ValidasiForm;
use Illuminate\Database\Eloquent\Collection;

class FormPermohonanService
{
    public function isFormValidFromVerifikator(AlurPermohonan $alurPermohonan)
    {
        $alurPermohonan->relationLoaded('permohonan') || $alurPermohonan->load('permohonan');
        if ($alurPermohonan->is_done) {
            return true;
        }

        if (!$alurPermohonan->validasiForm()->exists()) {
            return false;
        }

        return $alurPermohonan->validasiForm->first()->status == StatusValidasiEnum::VALID->value;
    }

    public function isFormNeedValidationFromVerifikator(AlurPermohonan $alurPermohonan)
    {
        $alurPermohonan->relationLoaded('permohonan') || $alurPermohonan->load('permohonan');
        if ($alurPermohonan->is_done) {
            return false;
        }

        return !$alurPermohonan->validasiForm()->exists();
    }

    public function getLastValidationFormByVerifikator(AlurPermohonan $alurPermohonan): ?ValidasiForm
    {
        $alurPermohonan->relationLoaded('permohonan') || $alurPermohonan->load('permohonan');

        // if (!$alurPermohonan->validasiForm->dd()->withTrashed()->dd()->exists()) {
        //     return null;
        // }

        $alurPermohonan->load(['validasiForm' => function ($query) {
            $query->withTrashed()->orderBy('created_at', 'desc');
        }]);

        if ($alurPermohonan->validasiForm->isEmpty()) {
            return null;
        }

        return $alurPermohonan->validasiForm->first();
    }

    public function getLastValidationFormByPermohonan(Permohonan $permohonan)
    {
        $permohonan->relationLoaded('alurPermohonan') || $permohonan->load('alurPermohonan');
        if ($permohonan->alurPermohonan->isNotEmpty() && !$permohonan->alurPermohonan->first()->relationLoaded('validasiForm')) {
            $permohonan->alurPermohonan->first()->load('validasiForm');
        }

        if ($permohonan->alurPermohonan->isEmpty()) {
            return null;
        }

        // get all validasi form from alur permohonan
        return $permohonan->alurPermohonan->where('is_done', false)->first()->validasiForm->first() ?? null;

    }

    public function isFormValidatedByVerifikator(AlurPermohonan $alurPermohonan)
    {
        $alurPermohonan->relationLoaded('permohonan') || $alurPermohonan->load('permohonan');

        return $alurPermohonan->validasiForm()->exists();
    }

    public function revisiFormByVerifikator(AlurPermohonan $alurPermohonan, $catatan): Permohonan
    {
        $alurPermohonan->relationLoaded('permohonan') || $alurPermohonan->load('permohonan');

        $alurPermohonan->validasiForm()->delete();
        $alurPermohonan->validasiForm()->create([
            'status' => StatusValidasiEnum::REVISI->value,
            'catatan' => $catatan
        ]);

        return $alurPermohonan->permohonan;
    }

    public function validFormByVerifikator(AlurPermohonan $alurPermohonan): Permohonan
    {
        $alurPermohonan->relationLoaded('permohonan') || $alurPermohonan->load('permohonan');

        $alurPermohonan->validasiForm()->delete();
        $alurPermohonan->validasiForm()->create([
            'status' => StatusValidasiEnum::VALID->value,
            'catatan' => null
        ]);

        return $alurPermohonan->permohonan;
    }

    public function isFormOnRevisi(Permohonan $permohonan)
    {
        $permohonan->relationLoaded('alurPermohonan') || $permohonan->load('alurPermohonan');
        if ($permohonan->alurPermohonan->isNotEmpty() && !$permohonan->alurPermohonan->first()->relationLoaded('validasiForm')) {
            $permohonan->alurPermohonan->first()->load('validasiForm');
        }

        // check every alurPermohonan on validasi form is revisi exist
        return $permohonan->alurPermohonan->filter(function ($alurPermohonan) {
            return $alurPermohonan->validasiForm->where('status', StatusValidasiEnum::REVISI->value)->isNotEmpty();
        })->isNotEmpty();
    }
}
