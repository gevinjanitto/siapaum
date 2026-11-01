<?php

namespace App\Services;

use App\Models\Permohonan;
use App\Models\AlurPermohonan;
use App\Models\BerkasPermohonan;
use Illuminate\Support\Facades\DB;
use App\Enums\JenisVerifikatorEnum;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceException;
use App\Services\VerifikatorService;

class BerkasPermohonanService
{
    public function getLastStatusAllAlurBerkasPermohonan(Permohonan $permohonan)
    {
        $verifikatorService = new VerifikatorService();
        $permohonan->relationLoaded('alurPermohonan') || $permohonan->load('alurPermohonan');
        $permohonan->relationLoaded('berkasPermohonan') || $permohonan->load('berkasPermohonan.validasiBerkas');
        if ($permohonan->alurPermohonan->isNotEmpty() && !$permohonan->alurPermohonan->first()->relationLoaded('verifikator')) {
            $permohonan->alurPermohonan->load('verifikator');
        }

        $a = $permohonan->alurPermohonan->filter(function ($alurPermohonan) use ($verifikatorService) {
            return $verifikatorService->isJenisVerifikatorApprovable($alurPermohonan);
        })->map(function ($alurPermohonan) use ($permohonan) {
            return collect([
                'alur_permohonan' => $alurPermohonan,
                'validasi_berkas' => $permohonan->berkasPermohonan->map(function ($berkasPermohonan) use ($alurPermohonan) {
                    $status = $this->getLastStatusBerkasByAlur($alurPermohonan, $berkasPermohonan);
                    $berkasPermohonan->is_revisi = $status->get('status') == 'revisi';
                    $berkasPermohonan->catatan = $status->get('catatan');
                    unset($berkasPermohonan->validasiBerkas);
                    return $berkasPermohonan;
                }),
            ]);
        });
        return $a;
    }

    public function getLastStatusAllBerkasByAlur(AlurPermohonan $alurPermohonan)
    {
        $alurPermohonan->relationLoaded('permohonan') || $alurPermohonan->load('permohonan');
        $alurPermohonan->permohonan->relationLoaded('berkasPermohonan') || $alurPermohonan->permohonan->load('berkasPermohonan.validasiBerkas');

        $alurPermohonan->permohonan->berkasPermohonan->map(function ($berkasPermohonan) use ($alurPermohonan) {
            if (
                $alurPermohonan->jenis_verifikator == JenisVerifikatorEnum::JF->value ||
                $alurPermohonan->jenis_verifikator == JenisVerifikatorEnum::PENANDATANGAN->value
            ) {
                $berkasPermohonan->is_need_validation = false;
            } else {
                if ($this->isLastStatusValidasiBerkasIsExist($alurPermohonan, $berkasPermohonan) && $this->isLastStatusValidasiBerkasIsVerified($alurPermohonan, $berkasPermohonan)) {
                    $berkasPermohonan->is_need_validation = false;
                } else {
                    if ($this->isBerkasOnRevisi($berkasPermohonan)) {
                        $berkasPermohonan->is_need_validation = true;
                    } else {
                        if ($this->isLastStatusValidasiBerkasIsValid($alurPermohonan, $berkasPermohonan)) {
                            $berkasPermohonan->is_need_validation = false;
                        } else {
                            $berkasPermohonan->is_need_validation = true;
                        }
                    }
                }
            }
        });
        return $alurPermohonan->permohonan->berkasPermohonan;
    }

    public function isBerkasOnRevisi(BerkasPermohonan $berkasPermohonan)
    {
        return $berkasPermohonan->is_revisi;
    }

    public function isLastStatusValidasiBerkasIsValid(AlurPermohonan $alurPermohonan, BerkasPermohonan $berkasPermohonan)
    {
        $status = $this->getLastStatusBerkasByAlur($alurPermohonan, $berkasPermohonan);
        return $status?->get('status') == 'valid';
    }

    public function isLastStatusValidasiBerkasIsVerified(AlurPermohonan $alurPermohonan, BerkasPermohonan $berkasPermohonan)
    {
        $status = $this->getLastStatusBerkasByAlur($alurPermohonan, $berkasPermohonan);
        return $status?->get('status') != 'pending' && $status;
    }

    public function isLastStatusValidasiBerkasIsExist(AlurPermohonan $alurPermohonan, BerkasPermohonan $berkasPermohonan)
    {
        $status = $this->getLastStatusBerkasByAlur($alurPermohonan, $berkasPermohonan);
        return $status != null;
    }

    public function getLastStatusBerkasByAlur(AlurPermohonan $alurPermohonan, BerkasPermohonan $berkasPermohonan)
    {
        $verifikatorService = new VerifikatorService();
        if (!$verifikatorService->isJenisVerifikatorApprovable($alurPermohonan)) {
            throw new ServiceException('Jenis verifikator tidak dapat melakukan validasi berkas');
        }

        $berkasPermohonan->relationLoaded('validasiBerkas') || $berkasPermohonan->load('validasiBerkas');
        // with trashed validasi berkas
        $berkasPermohonan = $berkasPermohonan
            ->validasiBerkas
            ->where('alur_permohonan_id', $alurPermohonan->id)
            ->filter(function ($validasiBerkas) {
                return $validasiBerkas->deleted_at == null;
            })
            ->sortByDesc('created_at')
            ->first();

        if (!$berkasPermohonan) {
            return null;
        } else {
            return collect([
                'status' => $berkasPermohonan->status,
                'catatan' => $berkasPermohonan->catatan,
                'created_at' => $berkasPermohonan->created_at,
            ]);
        }
    }

    public function revisiBerkasByVerifikator(AlurPermohonan $alurPermohonan, BerkasPermohonan $berkasPermohonan, string $catatan)
    {
        $verifikatorService = new VerifikatorService();
        if (!$verifikatorService->isJenisVerifikatorApprovable($alurPermohonan)) {
            throw new ServiceException('Jenis verifikator tidak dapat melakukan validasi berkas');
        }

        DB::beginTransaction();
        try {
            $berkasPermohonan->relationLoaded('validasiBerkas') || $berkasPermohonan->load('validasiBerkas');
            // delete all validasi berkas
            $berkasPermohonan->validasiBerkas()->delete();
            // create new validasi berkas
            $berkasPermohonan->validasiBerkas()->create([
                'alur_permohonan_id' => $alurPermohonan->id,
                'status' => 'revisi',
                'catatan' => $catatan,
            ]);
            // update berkas permohonan
            $berkasPermohonan->is_revisi = true;
            $berkasPermohonan->catatan = $catatan;
            $berkasPermohonan->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('error')->error($e->getFile() . $e->getLine() . $e->getMessage());
            throw new ServiceException('Gagal melakukan revisi berkas');
        }

        return $berkasPermohonan;
    }

    public function validBerkasByVerifikator(AlurPermohonan $alurPermohonan, BerkasPermohonan $berkasPermohonan)
    {
        $verifikatorService = new VerifikatorService();
        if (!$verifikatorService->isJenisVerifikatorApprovable($alurPermohonan)) {
            throw new ServiceException('Jenis verifikator tidak dapat melakukan validasi berkas');
        }

        DB::beginTransaction();
        try {
            $berkasPermohonan->relationLoaded('validasiBerkas') || $berkasPermohonan->load('validasiBerkas');
            // delete all validasi berkas
            $berkasPermohonan->validasiBerkas()->delete();
            // create new validasi berkas
            $berkasPermohonan->validasiBerkas()->create([
                'alur_permohonan_id' => $alurPermohonan->id,
                'status' => 'valid',
            ]);
            // update berkas permohonan
            $berkasPermohonan->is_revisi = false;
            $berkasPermohonan->catatan = null;
            $berkasPermohonan->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('error')->error($e->getFile() . $e->getLine() . $e->getMessage());
            throw new ServiceException('Gagal melakukan validasi berkas');
        }

        return $berkasPermohonan;
    }

    public function isAllBerkasValidFromVerifikator(AlurPermohonan $alurPermohonan)
    {
        $alurPermohonan->relationLoaded('permohonan') || $alurPermohonan->load('permohonan');
        if($alurPermohonan->is_done) {
            return true;
        }
        $alurPermohonan->permohonan->relationLoaded('berkasPermohonan') || $alurPermohonan->permohonan->load(['berkasPermohonan' => function ($query) use ($alurPermohonan) {
            $query->with(['validasiBerkas' => function ($query) use ($alurPermohonan) {
                $query->withTrashed();
            }]);
        }]);

        // with trashed validasi berkas
        $berkasPermohonan = $alurPermohonan->permohonan->berkasPermohonan
            // ->whereNotNull('filepath')
            ->filter(function ($berkasPermohonan) use ($alurPermohonan) {
                if ($alurPermohonan->jenis_verifikator == JenisVerifikatorEnum::JF->value || $alurPermohonan->jenis_verifikator == JenisVerifikatorEnum::PENANDATANGAN->value) {
                    return true;
                }
                return !$this->isLastStatusValidasiBerkasIsValid($alurPermohonan, $berkasPermohonan);
            });

        return $berkasPermohonan->isEmpty();
    }

    public function isLastStatusValidasiBerkasIsRevisi(AlurPermohonan $alurPermohonan, BerkasPermohonan $berkasPermohonan)
    {
        $status = $this->getLastStatusBerkasByAlur($alurPermohonan, $berkasPermohonan);
        return $status?->get('status') == 'revisi';
    }

    public function isAllBerkasVerifiedFromVerifikator(AlurPermohonan $alurPermohonan)
    {
        $alurPermohonan->relationLoaded('permohonan') || $alurPermohonan->load('permohonan');
        $alurPermohonan->permohonan->relationLoaded('berkasPermohonan') || $alurPermohonan->permohonan->load('berkasPermohonan.validasiBerkas');

        $berkasPermohonan = $alurPermohonan->permohonan->berkasPermohonan
            // ->whereNotNull('filepath')
            ->filter(function ($berkasPermohonan) use ($alurPermohonan) {
                return !$this->isLastStatusValidasiBerkasIsExist($alurPermohonan, $berkasPermohonan);
            });

        return $berkasPermohonan->isEmpty();
    }
}
