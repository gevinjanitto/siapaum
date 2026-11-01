<?php

namespace App\Http\Controllers\Api;

use App\Models\Reklame;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\FormJenisIzin;
use Illuminate\Support\Carbon;
use App\Models\RegistrasiReklame;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Enums\JenisReklameEnum;
use App\Enums\AreaPemasanganReklameEnum;
use App\Http\Resources\ReklameResource;
use App\Http\Requests\FormReklameRequest;
use App\Http\Requests\RegistrasiReklameRequest;
use App\Http\Resources\RegistrasiReklameResource;
use App\Http\Resources\PermohonanReklameCollection;
use App\Http\Resources\RegistrasiReklameCollection;

class ReklameController extends Controller
{
    public function index(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 10);
            $reklame = RegistrasiReklame::orderBy('created_at', 'desc')
                ->paginate($per_page);

            return ResponseFormatter::successPaginate(
                new RegistrasiReklameCollection($reklame),
                'Data berhasil diambil'
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Something went wrong', 500);
        }
    }

    public function show($id)
    {
        try {
            $reklame = RegistrasiReklame::with('formReklame')->find($id);
            if (!$reklame) {
                return ResponseFormatter::error([
                    'message' => 'Data tidak ditemukan',
                ], 'Data tidak ditemukan', 404);
            }

            return ResponseFormatter::success(
                new ReklameResource($reklame),
                'Data berhasil diambil'
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Something went wrong', 500);
        }
    }

    public function initReklame(RegistrasiReklameRequest $request)
    {
        try {
            $reklame = RegistrasiReklame::create([
                'user_id' => auth()->id(),
                'nama' => $request->nama,
                'nik' => $request->nik,
                'npwp' => $request->npwp,
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat_perusahaan' => $request->alamat_perusahaan,
                'nomor_telepon' => $request->nomor_telepon,
            ]);

            $reklame->update([
                'nomor_registrasi' => 'BLL/' . time() . '/' . auth()->id() . '/9/' . $reklame->id,
            ]);

            return ResponseFormatter::success(
                new RegistrasiReklameResource($reklame),
                'Data berhasil disimpan'
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Something went wrong', 500);
        }
    }

    public function storeReklame(FormReklameRequest $request, $id)
    {
        try {
            $registrasi_reklame = RegistrasiReklame::find($id);
            if (!$registrasi_reklame) {
                return ResponseFormatter::error([
                    'message' => 'Data tidak ditemukan',
                ], 'Data tidak ditemukan', 404);
            }

            $filepath = $request->file('image')->store('public/reklame-images');


            $reklame = $registrasi_reklame->reklame()->create([
                'image_filepath' => $filepath,
                'is_from_sireko' => true,
            ]);

            $jenis_izin = FormJenisIzin::where('jenis_izin_id', 9)
                ->get();

            foreach ($jenis_izin as $izin) {
                if ($izin->kode_isian == 'NAMA_PERUSAHAAN') {
                    $reklame->formReklame()->create([
                        'kode_isian' => $izin->kode_isian,
                        'tipe' => $izin->tipe,
                        'label' => $izin->label,
                        'value' => $registrasi_reklame->nama_perusahaan,
                        'urutan' => $izin->urutan,
                    ]);
                    continue;
                }

                if ($izin->kode_isian == 'HP/TELP') {
                    $reklame->formReklame()->create([
                        'kode_isian' => $izin->kode_isian,
                        'tipe' => $izin->tipe,
                        'label' => $izin->label,
                        'value' => $registrasi_reklame->nomor_telepon,
                        'urutan' => $izin->urutan,
                    ]);
                    continue;
                }

                if ($izin->kode_isian == 'ALAMAT') {
                    $reklame->formReklame()->create([
                        'kode_isian' => $izin->kode_isian,
                        'tipe' => $izin->tipe,
                        'label' => $izin->label,
                        'value' => $registrasi_reklame->alamat_perusahaan,
                        'urutan' => $izin->urutan,
                    ]);
                    continue;
                }

                if ($izin->kode_isian == 'LAMA_PEMASANGAN') {
                    $tanggal_awal = Carbon::createFromFormat('d-m-Y', $request->input('TGL_MULAI'));
                    $tanggal_akhir = Carbon::createFromFormat('d-m-Y', $request->input('TGL_AKHIR'));
                    $lama_pemasangan = $tanggal_awal->diffInDays($tanggal_akhir);
                    $reklame->formReklame()->create([
                        'kode_isian' => $izin->kode_isian,
                        'tipe' => $izin->tipe,
                        'label' => $izin->label,
                        'value' => $lama_pemasangan,
                        'urutan' => $izin->urutan,
                    ]);
                    continue;
                }

                $reklame->formReklame()->create([
                    'kode_isian' => $izin->kode_isian,
                    'tipe' => $izin->tipe,
                    'label' => $izin->label,
                    'value' => $request->input($izin->kode_isian),
                    'urutan' => $izin->urutan,
                ]);
            }

            return ResponseFormatter::success(
                new ReklameResource($registrasi_reklame),
                'Data berhasil disimpan'
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Something went wrong', 500);
        }
    }

    public function getPermohonanReklame(Request $request)
    {
        $request->validate([
            'per_page' => 'sometimes|integer|min:1',
            'status_izin' => 'sometimes|in:belum,pending,selesai,expired',
            'status_bongkar' => 'sometimes|in:belum,sudah',
            'days_to_bongkar' => 'sometimes|integer|min:1',
            'no_reg' => 'sometimes|string',
            'nama_wajib_pajak' => 'sometimes|string',
        ]);

        // $reklame = Reklame::whereHas('permohonan')->get();
        // return $reklame;

        $per_page = $request->input('per_page', 10);

        $query = Reklame::with('permohonan.formPermohonan', 'registrasiReklame');
        if ($request->has('status_izin')) {
            $status_izin = $request->input('status_izin');
            if ($status_izin === 'belum') {
                $query->whereDoesntHave('permohonan')
                    ->orWhereHas('permohonan', function ($q) {
                        $q->where('status', 'pending')
                        ->where('is_expired', false);
                    });
            } elseif ($status_izin === 'pending') {
                $query->whereHas('permohonan', function ($q) {
                    $q->where('status', '!=', 'pending')
                        ->where('status', '!=', 'selesai')
                        ->where('is_expired', false);
                });
            } elseif ($status_izin === 'selesai') {
                $query->whereHas('permohonan', function ($q) {
                    $q->where('status', 'selesai')
                        ->where('is_expired', false);
                });
            }elseif ($status_izin === 'expired') {
                $query->whereHas('permohonan', function ($q) {
                    $q->whereHas('formPermohonan', function ($q2) {
                        $q2->where('kode_isian', 'TGL_AKHIR')
                            ->whereDate('value', '<', Carbon::now()->format('Y-m-d'));
                    });
                });
            }
        }

        if ($request->has('status_bongkar')) {
            $status_bongkar = $request->input('status_bongkar');
            if ($status_bongkar === 'belum') {
                $query->where('is_bongkar', false);
            } elseif ($status_bongkar === 'sudah') {
                $query->where('is_bongkar', true);
            }
        }

        if ($request->has('days_to_bongkar')) {
            $days_to_bongkar = $request->input('days_to_bongkar');
            $target_date = Carbon::now()->addDays($days_to_bongkar)->format('Y-m-d');

            $query->whereHas('permohonan', function ($q) use ($target_date) {
                $q->whereHas('formPermohonan', function ($q2) use ($target_date) {
                    $q2->where('kode_isian', 'TGL_AKHIR')
                        ->whereDate('value', '<=', $target_date)
                        ->whereDate('value', '>=', Carbon::now()->format('Y-m-d'));
                });
            });
        }

        if ($request->has('no_reg')) {
            $no_reg = $request->input('no_reg');
            $query->whereHas('permohonan', function ($q) use ($no_reg) {
                $q->where('nomor_registrasi', 'like', '%' . $no_reg . '%');
            });
        }

        if ($request->has('nama_wajib_pajak')) {
            $nama_wajib_pajak = $request->input('nama_wajib_pajak');
            $query->whereHas('permohonan', function ($q) use ($nama_wajib_pajak) {
                $q->whereHas('formPermohonan', function ($q2) use ($nama_wajib_pajak) {
                    $q2->where('kode_isian', 'NAMA_PERUSAHAAN')
                        ->where('value', 'like', '%' . $nama_wajib_pajak . '%');
                });
            });
        }

        $reklame = $query->orderBy('created_at', 'desc')
            ->paginate($per_page);

        return ResponseFormatter::successPaginate(
            new PermohonanReklameCollection($reklame),
            'Data berhasil diambil'
        );
    }

    public function getPermohonanReklameById($id)
    {
        try {
            $reklame = Reklame::with([
                'permohonan.formPermohonan',
                'permohonan.kelengkapanPermohonan'
            ])->find($id);

            if (!$reklame) {
                return ResponseFormatter::error([
                    'message' => 'Data tidak ditemukan',
                ], 'Data tidak ditemukan', 404);
            }

            if (!$reklame->permohonan) {
                return ResponseFormatter::error([
                    'message' => 'Permohonan tidak ditemukan',
                ], 'Permohonan tidak ditemukan', 404);
            }

            // Map formPermohonan to array of kode_isian and value
            $form_data = $reklame->permohonan->formPermohonan->map(function ($form) {
                return [
                    'kode_isian' => $form->kode_isian,
                    'value' => $form->value,
                    'label' => $form->label,
                ];
            });

            // Map kelengkapanPermohonan to array of kode_isian and value
            $kelengkapan_data = $reklame->permohonan->kelengkapanPermohonan->map(function ($kelengkapan) {
                return [
                    'kode_isian' => $kelengkapan->kode_isian,
                    'value' => $kelengkapan->value,
                    'label' => $kelengkapan->label,
                ];
            });

            $data = [
                'id' => $reklame->permohonan->id,
                'nomor_registrasi' => $reklame->permohonan->nomor_registrasi,
                'nama' => $reklame->permohonan->nama,
                'nik' => $reklame->permohonan->nik,
                'npwp' => $reklame->permohonan->npwp,
                'status' => $reklame->permohonan->status,
                'status_name' => $reklame->permohonan->status_name,
                'created_at' => $reklame->permohonan->created_at,
                'form_permohonan' => $form_data,
                'kelengkapan_permohonan' => $kelengkapan_data,
            ];

            return ResponseFormatter::success(
                $data,
                'Data berhasil diambil'
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Something went wrong', 500);
        }
    }

    public function getSyaratAreaPemasangan()
    {
        try {
            $area_pemasangan = collect(AreaPemasanganReklameEnum::cases())->map(function ($area) {
                return [
                    'value' => $area->value,
                    'label' => $area->deskripsi(),
                ];
            });

            return ResponseFormatter::success(
                $area_pemasangan,
                'Data berhasil diambil'
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Something went wrong', 500);
        }
    }

    public function getSyaratJenisReklame()
    {
        try {
            $jenis_reklame = collect(JenisReklameEnum::cases())->map(function ($jenis) {
                return [
                    'value' => $jenis->value,
                    'label' => $jenis->deskripsi(),
                ];
            });

            return ResponseFormatter::success(
                $jenis_reklame,
                'Data berhasil diambil'
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Something went wrong', 500);
        }
    }

    public function storeBongkar($id)
    {
        try {
            $reklame = Reklame::find($id);

            if (!$reklame) {
                return ResponseFormatter::error([
                    'message' => 'Data reklame tidak ditemukan',
                ], 'Data tidak ditemukan', 404);
            }

            if ($reklame->is_bongkar) {
                return ResponseFormatter::error([
                    'message' => 'Reklame sudah dibongkar',
                ], 'Data sudah dibongkar', 400);
            }

            $reklame->update([
                'is_bongkar' => true,
            ]);

            // Clear cache when reklame is bongkar
            Cache::forget('reklame_koordinat_list');

            return ResponseFormatter::success(
                [
                    'id' => $reklame->id,
                    'is_bongkar' => $reklame->is_bongkar,
                ],
                'Reklame berhasil dibongkar'
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Something went wrong', 500);
        }
    }

    public function getKoordinat(Request $request)
    {
        try {
            $cacheKey = 'reklame_koordinat_list';
            $cacheDuration = 1; // Cache for 1 minute

            $koordinat_list = Cache::remember($cacheKey, $cacheDuration * 60, function () {
                $today = Carbon::now()->format('Y-m-d');

                return Reklame::with([
                    'permohonan.formPermohonan'
                ])
                ->whereHas('permohonan', function ($query) use ($today) {
                    $query->whereHas('formPermohonan', function ($q) use ($today) {
                        $q->where('kode_isian', 'TGL_AKHIR')
                          ->whereDate('value', '>=', $today);
                    });
                })
                ->get()
                ->map(function ($reklame) {
                    $titik_koordinat = null;
                    $tanggal_akhir = null;

                    if ($reklame->permohonan && $reklame->permohonan->formPermohonan) {
                        foreach ($reklame->permohonan->formPermohonan as $form) {
                            if ($form->kode_isian === 'TITIK_KOORDINAT') {
                                $titik_koordinat = $form->value;
                            }
                            if ($form->kode_isian === 'TGL_AKHIR') {
                                $tanggal_akhir = $form->value;
                            }
                        }
                    }

                    return [
                        'reklame_id' => $reklame->id,
                        'is_bongkar' => $reklame->is_bongkar,
                        'titik_koordinat' => $titik_koordinat,
                        'tanggal_akhir_pemasangan' => $tanggal_akhir,
                    ];
                })
                ->filter(function ($item) {
                    // Filter out items without koordinat
                    return !is_null($item['titik_koordinat']);
                })
                ->values();
            });

            return ResponseFormatter::success(
                $koordinat_list,
                'Data koordinat berhasil diambil'
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Something went wrong', 500);
        }
    }
}
