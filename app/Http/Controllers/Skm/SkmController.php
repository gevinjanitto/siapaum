<?php

namespace App\Http\Controllers\Skm;

use App\Models\JenisIzin;
use App\Models\LayananSkm;
use Illuminate\Http\Request;
use App\Enums\PendidikanEnum;
use App\Enums\JenisKelaminEnum;
use App\Models\GroupLayananSkm;
use App\Enums\JenisPekerjaanEnum;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Models\KuesionerPertanyaan;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyLayananRequest;

class SkmController extends Controller
{
    public function jenisLayanan(Request $request)
    {
        $jenisIzin = JenisIzin::get()->map(function ($item) {
            return [
                'id' => $item->ulid,
                'group_id' => null,
                'nama' => $item->nama,
                'is_layanan_skm' => false,
            ];
        });

        $layananSkm = LayananSkm::with('groupLayananSkm')->get()->map(function ($item) {
            return [
                'id' => $item->ulid,
                'group_id' => $item->groupLayananSkm->ulid,
                'nama' => $item->nama,
                'is_layanan_skm' => true,
            ];
        });

        // merge
        $jenisLayanan = $jenisIzin->merge($layananSkm);
        return ResponseFormatter::success(
            $jenisLayanan,
            'Data jenis layanan berhasil diambil'
        );
    }

    public function pertanyaanOpsi(Request $request)
    {
        if ($request->has('group_layanan_skm_id')) {
            $groupLayananSkm = GroupLayananSkm::with('kuesionerPertanyaan')->where('ulid', $request->group_layanan_skm_id)->first();
            if (!$groupLayananSkm) {
                return ResponseFormatter::error(
                    null,
                    'Group layanan skm tidak ditemukan',
                    404
                );
            }

            if ($groupLayananSkm->kuesionerPertanyaan->isEmpty()) {
                $pertanyaan = KuesionerPertanyaan::with('kuesionerOpsi')
                    ->whereNull('group_layanan_skm_id')
                    ->get();
            } else {
                $pertanyaan = $groupLayananSkm->kuesionerPertanyaan;
            }
        } else {
            $pertanyaan = KuesionerPertanyaan::with('kuesionerOpsi')
                ->whereNull('group_layanan_skm_id')
                ->get();
        }

        $pertanyaan = $pertanyaan->map(function ($item) {
            return [
                'id' => $item->id,
                'pertanyaan' => $item->pertanyaan,
                'opsi' => $item->kuesionerOpsi->map(function ($opsi) {
                    return [
                        'id' => $opsi->id,
                        'opsi' => $opsi->opsi,
                    ];
                }),
            ];
        });
        return ResponseFormatter::success(
            $pertanyaan,
            'Data pertanyaan jawaban berhasil diambil'
        );
    }

    public function jenisPekerjaan(Request $request)
    {
        $jenisPekerjaan = JenisPekerjaanEnum::arrayValues();
        return ResponseFormatter::success(
            $jenisPekerjaan,
            'Data jenis pekerjaan berhasil diambil'
        );
    }

    public function pendidikan(Request $request)
    {
        $pendidikan = PendidikanEnum::arrayValues();
        return ResponseFormatter::success(
            $pendidikan,
            'Data pendidikan berhasil diambil'
        );
    }

    public function jenisKelamin(Request $request)
    {
        $jenisKelamin = JenisKelaminEnum::arrayValues();
        return ResponseFormatter::success(
            $jenisKelamin,
            'Data jenis kelamin berhasil diambil'
        );
    }

    public function groupLayananSkm(Request $request)
    {
        $groupLayananSkm = GroupLayananSkm::with('layananSkm')->get()->map(function ($item) {
            return [
                'id' => $item->ulid,
                'nama' => $item->nama,
                'image_url' => $item->image_url,
                'jenis_layanan' => $item->layananSkm->map(function ($layanan) {
                    return [
                        'id' => $layanan->ulid,
                        'nama' => $layanan->nama,
                    ];
                }),
            ];
        });

        return ResponseFormatter::success(
            $groupLayananSkm,
            'Data group layanan skm berhasil diambil'
        );
    }

    public function surveyLayanan(SurveyLayananRequest $request)
    {
        $layananSkm = LayananSkm::with('groupLayananSkm')->where('ulid', $request->jenis_layanan_id)->first();
        $layananJenisIzin = JenisIzin::where('ulid', $request->jenis_layanan_id)->first();
        if (!$layananSkm && !$layananJenisIzin) {
            return ResponseFormatter::error(
                null,
                'Layanan tidak ditemukan',
                404
            );
        }

        $kuesionerPertanyaans = KuesionerPertanyaan::with('kuesionerOpsi')->where('group_layanan_skm_id', $layananSkm?->groupLayananSkm?->id)->get();
        if ($kuesionerPertanyaans->isEmpty()) {
            $kuesionerPertanyaans = KuesionerPertanyaan::whereNull('group_layanan_skm_id')->with('kuesionerOpsi')->get();
        }

        $kuesionerIds = $kuesionerPertanyaans->pluck('id')->toArray();
        // validate all pertanyaan id is exists in request
        $diff = array_diff($kuesionerIds, array_column($request->jawaban, 'pertanyaan_id'));
        if (!empty($diff)) {
            return ResponseFormatter::error(
                null,
                'Kuesioner tidak valid. Semua pertanyaan harus dijawab',
                400
            );
        }

        // validate if every opsi is belongs to pertanyaan
        $invalidIds = [];
        foreach ($request->jawaban as $jawaban) {
            $kuesionerPertanyaan = KuesionerPertanyaan::find($jawaban['pertanyaan_id']);
            if (!$kuesionerPertanyaan->kuesionerOpsi->contains('id', $jawaban['opsi_id'])) {
                $invalidIds[] = $jawaban['pertanyaan_id'];
            }
        }

        if (!empty($invalidIds)) {
            return ResponseFormatter::error(
                [
                    'invalid_pertanyaaan_id' => $invalidIds,
                ],
                'Kuesioner opsi_id tidak valid pada pertanyaan',
                400
            );
        }

        DB::beginTransaction();
        try {
            if ($layananSkm) {
                $layanan = $layananSkm;
            } else {
                $layanan = $layananJenisIzin;
            }

            $kuesioner = $layanan->kuesioner()->create([
                'nama' => $request->nama,
                'nomor_telepon' => $request->nomor_telepon,
                'email' => $request->email,
                'jenis_kelamin' => $request->jenis_kelamin,
                'jenis_layanan' => $layanan->nama,
                'pendidikan' => $request->pendidikan,
                'pekerjaan' => $request->pekerjaan,
                'is_from_skm' => true,
            ]);

            // validate value in kuesioner is belong to kuesioner opsi key
            foreach ($request->jawaban as $jawaban) {
                $kuesionerPertanyaan = KuesionerPertanyaan::find($jawaban['pertanyaan_id']);
                if (!$kuesionerPertanyaan->kuesionerOpsi->contains('id', $jawaban['opsi_id'])) {
                    DB::rollBack();
                    return ResponseFormatter::error(
                        null,
                        'Kuesioner tidak valid',
                        400
                    );
                }
            }

            $kuesioner->kuesionerJawaban()->createMany(
                array_map(function ($jawaban)  use ($kuesionerPertanyaans) {
                    return [
                        'kuesioner_pertanyaan_id' => $jawaban['pertanyaan_id'],
                        'kuesioner_opsi_id' => $jawaban['opsi_id'],
                        'pertanyaan' => $kuesionerPertanyaans->where('id', $jawaban['pertanyaan_id'])->first()?->pertanyaan,
                        'opsi' => $kuesionerPertanyaans->where('id', $jawaban['pertanyaan_id'])->first()?->kuesionerOpsi->where('id', $jawaban['opsi_id'])->first()?->opsi,
                        'point' => $kuesionerPertanyaans->where('id', $jawaban['pertanyaan_id'])->first()?->kuesionerOpsi->where('id', $jawaban['opsi_id'])->first()?->point,
                    ];
                }, $request->jawaban)
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return ResponseFormatter::error(
                null,
                'Internal Server Error',
                500
            );
        }

        return ResponseFormatter::success(
            [
                'layanan' => $layanan->nama,
            ],
            'Survey berhasil disimpan'
        );
    }
}
