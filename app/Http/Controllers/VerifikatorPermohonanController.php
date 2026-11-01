<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\JenisIzin;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use App\Models\BerkasPermohonan;
use Illuminate\Support\Facades\DB;
use App\Enums\JenisVerifikatorEnum;
use App\Enums\StatusPermohonanEnum;
use App\Services\PermohonanService;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceException;
use App\Services\VerifikatorService;
use App\Services\FormPermohonanService;
use Illuminate\Support\Facades\Storage;
use App\Services\BerkasPermohonanService;
use App\Notifications\PembayaranPajakReklameToPemohonNotication;
use App\Notifications\PermohonanStatusIsChangeForPublicNotification;

class VerifikatorPermohonanController extends Controller
{
    public function index(Request $request)
    {
        $jenis_izins = JenisIzin::all();
        return view('pages.verifikator.permohonan.index', compact(
            'jenis_izins',
        ));
    }

    public function verifikasiIndex(Request $request)
    {
        $jenis_izins = JenisIzin::all();
        return view('pages.verifikator.verifikasi.index', compact(
            'jenis_izins'
        ));
    }

    public function show(Request $request, Permohonan $permohonan, PermohonanService $permohonanService, BerkasPermohonanService $berkasPermohonanService, VerifikatorService $verifikatorService, FormPermohonanService $formPermohonanService)
    {
        try {
            $permohonan->load('user', 'alurPermohonan');
            $steps = $permohonanService->getStepAlurPermohonan($permohonan, true);
            $is_verifikator_turn = $verifikatorService->isVerifikatorTurn($permohonan, auth()->user());
            $is_verifikator_approvable_berkas = $verifikatorService->isVerifikatorApprovableBerkas($permohonan, auth()->user());
            $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($permohonan, auth()->user());
            $is_can_verified = $permohonanService->isPermohonanCanVerified($permohonan);
            if (
                $alur_permohonan->jenis_verifikator == JenisVerifikatorEnum::JF->value ||
                $alur_permohonan->jenis_verifikator == JenisVerifikatorEnum::PENANDATANGAN->value
            ) {
                $is_form_valid = true;
                $is_all_berkas_valid = true;
            } else {
                $is_form_valid = $formPermohonanService->isFormValidFromVerifikator($alur_permohonan);
                $is_all_berkas_valid = $berkasPermohonanService->isAllBerkasValidFromVerifikator($alur_permohonan);
            }
            $last_validation_form = $formPermohonanService->getLastValidationFormByVerifikator($alur_permohonan);
            $is_form_need_validation = $formPermohonanService->isFormNeedValidationFromVerifikator($alur_permohonan);
            $berkas_permohonans = $berkasPermohonanService->getLastStatusAllBerkasByAlur($alur_permohonan);
            $is_all_jf_done = $permohonan->alurPermohonan->where('jenis_verifikator', JenisVerifikatorEnum::JF->value)->where('is_done', false)->isEmpty();
            $is_all_bo_done = $permohonan->alurPermohonan->where('jenis_verifikator', JenisVerifikatorEnum::BO->value)->where('is_done', false)->isEmpty();
            return view('pages.verifikator.verifikasi.validasi', compact(
                'permohonan',
                'berkas_permohonans',
                'steps',
                'is_verifikator_approvable_berkas',
                'is_verifikator_turn',
                'alur_permohonan',
                'is_can_verified',
                'is_form_valid',
                'last_validation_form',
                'is_form_need_validation',
                'is_all_berkas_valid',
                'is_all_jf_done',
                'is_all_bo_done',
            ));
        } catch (ServiceException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            Log::error($e->getFile() . $e->getLine() . $e->getMessage() . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server');
        }
    }

    public function simpanVerifikasi(Request $request, Permohonan $permohonan, PermohonanService $permohonanService, BerkasPermohonanService $berkasPermohonanService, VerifikatorService $verifikatorService, FormPermohonanService $formPermohonanService)
    {
        $permohonan->load([
            'kelengkapanPermohonan'
        ]);

        if (!$permohonanService->isPermohonanCanVerified($permohonan)) {
            return redirect()->back()->with('error', 'Permohonan tidak dapat diverifikasi');
        }

        // cek apakah semua berkas sudah divalidasi, jika belum set status permohonan ke revisi. jika sudah set alur permohonan ke done dan update status permohonan ke verifikasi.
        $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($permohonan, auth()->user());


        if ($verifikatorService->isJenisVerifikatorApprovable($alur_permohonan)) {
            if (
                $berkasPermohonanService->isAllBerkasValidFromVerifikator($alur_permohonan) &&
                $formPermohonanService->isFormValidFromVerifikator($alur_permohonan)
            ) {
                // JF wajib sudah upload Surat pengantar permohonan
                if ($alur_permohonan->jenis_verifikator == JenisVerifikatorEnum::FO->value) {
                    // if (!$permohonan->surat_permohonan_rekomendasi_filepath) {
                    //     return redirect()->back()->with('error', 'Mohon unggah Surat pengantar permohonan terlebih dahulu sebelum dilanjutkan ke verifikator berikutnya');
                    // }

                    activity()
                        ->performedOn($permohonan)
                        ->causedBy(auth()->user())
                        ->log('Disetujui verifikator Front Office');

                } else if ($alur_permohonan->jenis_verifikator == JenisVerifikatorEnum::OPD->value) {
                    // handle reklame harus upload pajak reklame, skpd
                    if ($permohonan->jenis_izin_id == 9) {
                        $rules = [
                            'PAJAK_REKLAME' => 'required',
                            'PAJAK_REKLAME_TERBILANG' => 'required',
                            'NO_SKPD' => 'required',
                        ];
                        $validation_messages = [
                            'PAJAK_REKLAME.required' => 'Mohon unggah pajak reklame',
                            'PAJAK_REKLAME_TERBILANG.required' => 'Mohon unggah pajak reklame terbilang',
                            'NO_SKPD.required' => 'Mohon unggah nomor SKPD',
                        ];
                        if (!$permohonan->reklame?->skpd_filepath) {
                            return redirect()->back()->with('error', 'Mohon unggah SKPD terlebih dahulu sebelum dilanjutkan ke verifikator berikutnya');
                        }

                        $validated = $request->validate($rules, $validation_messages);

                        foreach ($validated as $key => $value) {
                            $kelengkapan_permohonan = $permohonan->kelengkapanPermohonan->where('kode_isian', $key)->first();
                            $kelengkapan_permohonan->value = $value;
                            $kelengkapan_permohonan->save();
                        }

                        if (!$permohonan->reklame?->bukti_bayar_filepath) {
                            return redirect()->back()->with('success', 'Data pajak SKPD berhasil disimpan. Pending unggah bukti bayar oleh anda atau pemohon. Untuk melanjutkan, mohon tunggu pemohon mengunggah bukti bayar atau anda dapat mengunggahnya terlebih dahulu');
                        }
                    }
                    // all permohonan
                    if (!$permohonan->surat_rekomendasi_filepath && $permohonan->jenis_izin_id != 9) {
                        return redirect()->back()->with('error', 'Mohon unggah Lampiran Teknis terlebih dahulu sebelum dilanjutkan ke verifikator berikutnya');
                    }

                    activity()
                        ->performedOn($permohonan)
                        ->causedBy(auth()->user())
                        ->log('Disetujui verifikator OPD');

                } else if ($alur_permohonan->jenis_verifikator == JenisVerifikatorEnum::BO->value) {
                    // cek if all surat kelengkapan uploaded
                    $rules = [];
                    foreach ($permohonan->kelengkapanPermohonan as $kelengkapan_permohonan) {
                        // blacklist reklame form
                        if ($permohonan->jenis_izin_id == 9 && in_array($kelengkapan_permohonan->kode_isian, [
                            'PAJAK_REKLAME',
                            'PAJAK_REKLAME_TERBILANG',
                            'NO_SKPD'
                        ])) {
                            continue;
                        }
                        $rules[$kelengkapan_permohonan->kode_isian] = 'required';
                        $validation_messages[$kelengkapan_permohonan->kode_isian . '.required'] = 'Mohon unggah ' . $kelengkapan_permohonan->label;
                    }

                    $validated = $request->validate($rules, $validation_messages);

                    foreach ($validated as $key => $value) {
                        $kelengkapan_permohonan = $permohonan->kelengkapanPermohonan->where('kode_isian', $key)->first();
                        $kelengkapan_permohonan->value = $value;
                        $kelengkapan_permohonan->save();
                    }

                    // generate pdf template for ijin terbit
                    try {
                        $filepath = $permohonanService->generateIzinTerbit($permohonan);
                    } catch (ServiceException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (Exception $e) {
                        Log::error($e->getFile() . $e->getLine() . $e->getMessage());
                        return redirect()->back()->with('error', 'Terjadi kesalahan pada server');
                    }

                    $permohonan->template_surat_filepath = $filepath;
                    $permohonan->save();

                    activity()
                        ->performedOn($permohonan)
                        ->causedBy(auth()->user())
                        ->log('Disetujui verifikator Back Office');
                }

                $permohonan->status = StatusPermohonanEnum::VERIFIKASI->value;
                $alur_permohonan->is_done = true;
                $alur_permohonan->save();
                $permohonan->save();

                return redirect()->route('verifikator.permohonan.index')->with('success', 'Permohonan berhasil diverifikasi');
            } else {
                if (!$berkasPermohonanService->isAllBerkasVerifiedFromVerifikator($alur_permohonan)) {
                    return redirect()->back()->with('error', 'Mohon validasi semua berkas terlebih dahulu sebelum melanjutkan');
                }
                if (!$formPermohonanService->isFormValidatedByVerifikator($alur_permohonan)) {
                    return redirect()->back()->with('error', 'Mohon validasi form detail permohonan terlebih dahulu sebelum melanjutkan');
                }
                $permohonan->status = StatusPermohonanEnum::REVISI->value;
                $permohonan->save();
                $permohonan->user->notify(new PermohonanStatusIsChangeForPublicNotification($permohonan));

                activity()
                    ->performedOn($permohonan)
                    ->causedBy(auth()->user())
                    ->log('Verifikator meminta revisi');

                return redirect()->route('verifikator.permohonan.index')->with('success', 'Permohonan berhasil dilakukan revisi');
            }
        } else {
            if ($alur_permohonan->jenis_verifikator == JenisVerifikatorEnum::PENANDATANGAN->value) {
                $request->validate([
                    'passphrase' => 'required',
                ]);
                try {
                    $filepath = $permohonanService->ttdIzinTerbit($permohonan, auth()->user(), $request->input('passphrase'));
                    $permohonan->status = StatusPermohonanEnum::SELESAI->value;
                    $permohonan->save();
                    $alur_permohonan->is_done = true;
                    $alur_permohonan->save();
                    $permohonan->user->notify(new PermohonanStatusIsChangeForPublicNotification($permohonan));

                    activity()
                        ->performedOn($permohonan)
                        ->causedBy(auth()->user())
                        ->log('Izin terbit telah ditandatangani');

                } catch (ServiceException $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                } catch (Exception $e) {
                    Log::error($e->getFile() . $e->getLine() . $e->getMessage());
                    return redirect()->back()->with('error', 'Terjadi kesalahan pada server');
                }
                return redirect()->route('verifikator.permohonan.index')->with('success', 'Permohonan berhasil diverifikasi');
            } else if ($alur_permohonan->jenis_verifikator == JenisVerifikatorEnum::JF->value) {
                $permohonan->status = StatusPermohonanEnum::VERIFIKASI->value;
                $alur_permohonan->is_done = true;
                $alur_permohonan->save();
                $permohonan->save();

                activity()
                    ->performedOn($permohonan)
                    ->causedBy(auth()->user())
                    ->log('Disetujui verifikator JF');
                return redirect()->route('verifikator.permohonan.index')->with('success', 'Permohonan berhasil diverifikasi');
            } else {
                return redirect()->back()->with('error', 'Permohonan tidak dapat diverifikasi');
            }
        }
    }

    public function updateDraftSk(Request $request, Permohonan $permohonan, PermohonanService $permohonanService, VerifikatorService $verifikatorService)
    {
        // validasi apakah user adalah verifikaor BO
        $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($permohonan, auth()->user());
        if ($alur_permohonan->jenis_verifikator != JenisVerifikatorEnum::BO->value) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        // validasi apakah seluruh bo sudah selesai dan jf belum selesai
        $is_all_bo_done = $permohonan->alurPermohonan->where('jenis_verifikator', JenisVerifikatorEnum::BO->value)->where('is_done', false)->isEmpty();
        $is_all_jf_done = $permohonan->alurPermohonan->where('jenis_verifikator', JenisVerifikatorEnum::JF->value)->where('is_done', false)->isEmpty();
        if (!($is_all_bo_done && !$is_all_jf_done)) {
            return redirect()->back()->with('error', 'Tidak dapat mengupdate draft SK sebelum BO selesai verifikasi dan JF belum selesai');
        }

        // validasi permohonan sedang dalam status verifikasi
        if ($permohonan->status != StatusPermohonanEnum::VERIFIKASI->value) {
            return redirect()->back()->with('error', 'Permohonan tidak dalam status verifikasi');
        }

        $permohonan->load(['kelengkapanPermohonan']);

        foreach ($permohonan->kelengkapanPermohonan as $kelengkapan_permohonan) {
            $rules[$kelengkapan_permohonan->kode_isian] = 'required';
            $validation_messages[$kelengkapan_permohonan->kode_isian . '.required'] = 'Mohon unggah ' . $kelengkapan_permohonan->label;
        }

        DB::beginTransaction();
        try {
            $validated = $request->validate($rules, $validation_messages);
            foreach ($validated as $key => $value) {
                $kelengkapan_permohonan = $permohonan->kelengkapanPermohonan->where('kode_isian', $key)->first();
                $kelengkapan_permohonan->value = $value;
                $kelengkapan_permohonan->save();
            }

            // generate pdf template for ijin terbit
            $filepath = $permohonanService->generateIzinTerbit($permohonan);
            $permohonan->template_surat_filepath = $filepath;
            $permohonan->save();

            DB::commit();
        } catch (ServiceException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getFile() . $e->getLine() . $e->getMessage() . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server');
        }
        return redirect()->back()->with('success', 'Draft SK berhasil diupdate');
    }

    public function generateUlangIzinTerbit(Request $request, $permohonan, PermohonanService $permohonanService)
    {
        $permohonan = Permohonan::find($permohonan);

        try {
            $filepath = $permohonanService->generateIzinTerbit($permohonan);
            $permohonan->template_surat_filepath = $filepath;
            $permohonan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Izin terbit berhasil digenerate ulang',
                'data' => [
                    'filepath' => Storage::url($filepath),
                ],
            ]);
        } catch (ServiceException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            Log::error($e->getFile() . $e->getLine() . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ]);
        }
    }

    // public function uploadSuratPermohonanRekomendasi(Request $request, Permohonan $permohonan, BerkasPermohonanService $berkasPermohonanService, VerifikatorService $verifikatorService)
    // {
    //     $request->validate([
    //         // vaidate berkas_key us  surat_permohonan_rekomendasi
    //         'berkas_key' => 'required|in:surat_permohonan_rekomendasi',
    //         'berkas' => 'required|file|mimes:pdf|max:2048',
    //     ]);

    //     // get alur
    //     $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($permohonan, auth()->user());
    //     // check is all berkas valid
    //     $is_all_berkas_valid = $berkasPermohonanService->isAllBerkasValidFromVerifikator($alur_permohonan);

    //     // cek apakah jenis verifikator FO, jika ya maka wajib upload Surat pengantar permohonan
    //     if ($alur_permohonan->jenis_verifikator != JenisVerifikatorEnum::FO->value) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Anda tidak memiliki akses. Hanya verifikator FO yang dapat mengunggah Surat pengantar permohonan',
    //         ]);
    //     }

    //     if (!$is_all_berkas_valid) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Mohon validasi semua berkas terlebih dahulu sebelum mengunggah Surat pengantar permohonan',
    //         ]);
    //     } else {
    //         $permohonan->surat_permohonan_rekomendasi_filepath = $request->file('berkas')->store('public/permohonan/surat_permohonan_rekomendasi');
    //         $permohonan->save();
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Surat pengantar permohonan berhasil diunggah',
    //     ]);
    // }

    public function uploadSuratRekomendasi(Request $request, Permohonan $permohonan, BerkasPermohonanService $berkasPermohonanService, VerifikatorService $verifikatorService)
    {
        $request->validate([
            // vaidate berkas_key us  surat_rekomendasi
            'berkas_key' => 'required|in:surat_rekomendasi',
            'berkas' => 'required|file|mimes:pdf|max:5000',
        ]);

        if ($permohonan->jenis_izin_id == 9) {
            return response()->json([
                'success' => false,
                'message' => 'Izin reklame tidak memerlukan Lampiran Teknis',
            ]);
        }

        // get alur
        $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($permohonan, auth()->user());
        // check is all berkas valid
        $is_all_berkas_valid = $berkasPermohonanService->isAllBerkasValidFromVerifikator($alur_permohonan);

        // cek apakah jenis verifikator OPD, jika ya maka wajib upload Lampiran Teknis
        if ($alur_permohonan->jenis_verifikator != JenisVerifikatorEnum::OPD->value) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses. Hanya verifikator OPD yang dapat mengunggah Lampiran Teknis',
            ]);
        }

        if (!$is_all_berkas_valid) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengunggah Lampiran Teknis sebelum semua berkas dinyatakan valid',
            ]);
        } else {
            $permohonan->surat_rekomendasi_filepath = $request->file('berkas')->store('public/permohonan/surat_rekomendasi');
            $permohonan->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Lampiran Teknis berhasil diunggah',
        ]);
    }

    public function uploadSkpd(Request $request, Permohonan $permohonan, BerkasPermohonanService $berkasPermohonanService, VerifikatorService $verifikatorService)
    {
        $request->validate([
            'berkas_key' => 'required|in:skpd',
            'berkas' => 'required|file|mimes:pdf|max:2048',
        ]);

        $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($permohonan, auth()->user());
        // check is all berkas valid
        $is_all_berkas_valid = $berkasPermohonanService->isAllBerkasValidFromVerifikator($alur_permohonan);

        if ($alur_permohonan->jenis_verifikator != JenisVerifikatorEnum::OPD->value) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses. Hanya verifikator OPD yang dapat mengunggah SKPD',
            ]);
        }

        if (!$is_all_berkas_valid) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengunggah SKPD sebelum semua berkas dinyatakan valid',
            ]);
        }

        if($permohonan->jenis_izin_id != 9) {
            return response()->json([
                'success' => false,
                'message' => 'Izin ini tidak memerlukan SKPD',
            ]);
        }

        if($permohonan->alurPermohonan()->where('jenis_verifikator', JenisVerifikatorEnum::JF->value)->first()->is_done) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengunggah SKPD ulang setelah JF selesai verifikasi',
            ]);
        }

        $permohonan->reklame->skpd_filepath = $request->file('berkas')->store('public/permohonan/reklame/skpd');
        $permohonan->reklame->save();

        $permohonan->user->notify(new PembayaranPajakReklameToPemohonNotication($permohonan));

        activity()
            ->performedOn($permohonan)
            ->causedBy(auth()->user())
            ->log('Verifikator mengunggah SKPD Reklame');

        return response()->json([
            'success' => true,
            'message' => 'SKPD berhasil diunggah',
        ]);
    }

    public function uploadBuktiBayarReklame(Request $request, Permohonan $permohonan, BerkasPermohonanService $berkasPermohonanService, VerifikatorService $verifikatorService)
    {
        $request->validate([
            'berkas_key' => 'required|in:bukti_bayar',
            'berkas' => 'required|file|mimes:pdf|max:2048',
        ]);

        $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($permohonan, auth()->user());
        // check is all berkas valid
        $is_all_berkas_valid = $berkasPermohonanService->isAllBerkasValidFromVerifikator($alur_permohonan);

        if ($alur_permohonan->jenis_verifikator != JenisVerifikatorEnum::OPD->value) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses. Hanya verifikator OPD yang dapat mengunggah bukti bayar',
            ]);
        }

        if (!$is_all_berkas_valid) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengunggah bukti bayar sebelum semua berkas dinyatakan valid',
            ]);
        }

        if($permohonan->jenis_izin_id != 9) {
            return response()->json([
                'success' => false,
                'message' => 'Izin ini tidak memerlukan bukti bayar',
            ]);
        }

        if($permohonan->alurPermohonan()->where('jenis_verifikator', JenisVerifikatorEnum::JF->value)->first()->is_done) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengunggah bukti bayar ulang setelah JF selesai verifikasi',
            ]);
        }

        $permohonan->reklame->bukti_bayar_filepath = $request->file('berkas')->store('public/permohonan/reklame/bukti_bayar');
        $permohonan->reklame->bukti_bayar_user_id = auth()->user()->id;
        $permohonan->reklame->save();

        return response()->json([
            'success' => true,
            'message' => 'Bukti bayar berhasil diunggah',
        ]);
    }

    public function uploadLampiranSk(Request $request, Permohonan $permohonan, BerkasPermohonanService $berkasPermohonanService, VerifikatorService $verifikatorService, PermohonanService $permohonanService)
    {
        $request->validate([
            'berkas_key' => 'required|in:lampiran_sk',
            'berkas' => 'required|file|mimes:pdf|max:2048',
        ]);

        $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($permohonan, auth()->user());
        // check is all berkas valid
        $is_all_berkas_valid = $berkasPermohonanService->isAllBerkasValidFromVerifikator($alur_permohonan);

        if ($alur_permohonan->jenis_verifikator != JenisVerifikatorEnum::BO->value) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses. Hanya verifikator BO yang dapat mengunggah lampiran SK',
            ]);
        }

        if (!$is_all_berkas_valid) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengunggah lampiran SK sebelum semua berkas dinyatakan valid',
            ]);
        }

        $permohonan->lampiran_sk_filepath = $request->file('berkas')->store('public/permohonan/lampiran_sk');
        $permohonan->save();

        return response()->json([
            'success' => true,
            'message' => 'Lampiran SK berhasil diunggah',
        ]);
    }

    // TABLE
    public function permohonanTable(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = Permohonan::whereHas('alurPermohonan', function ($query) {
                $query->where('verifikator_id', auth()->user()->id);
            })
                ->whereNotIn('status', [
                    StatusPermohonanEnum::PENDING->value
                ]);

            // Total records
            $totalRecords = $query->count();

            // order table
            if ($request->input('order.0.name') == 'waktu_pengajuan') {
                $query = $query->orderBy('created_at', $request->input('order.0.dir'));
            }

            // Filter records
            if ($search || $request->input('status') || $request->input('jenis_izin_id')) {
                if ($search) {
                    $query = $query->where(function ($query) use ($search) {
                        $query->where('nomor_registrasi', 'like', '%' . $search . '%')
                            ->orWhere('nama', 'like', '%' . $search . '%');
                    });
                }
                if ($request->input('status')) {
                    if ($request->input('status') == StatusPermohonanEnum::VERIFIKASI->value) {
                        $query = $query->whereIn('status', [
                            StatusPermohonanEnum::PERMOHONAN_BARU->value,
                            StatusPermohonanEnum::VERIFIKASI->value,
                        ]);
                    } else {
                        $query = $query->where('status', $request->input('status'));
                    }
                }
                if ($request->input('jenis_izin_id')) {
                    $query = $query->where('jenis_izin_id', $request->input('jenis_izin_id'));
                }

                // filtered records count
                $totalFiltered = $query->count();
            } else {
                $totalFiltered = $totalRecords;
            }

            // Offset and limit
            if ($start != 0 || $length != -1) {
                $query = $query->offset($start)
                    ->limit($length);
            }

            // Get data
            $records = $query
                ->get()
                ->map(function ($permohonan) {
                    $action = '<a href="' . route('verifikator.verifikasi.show', $permohonan->id) . '"><i class="isax-bold isax-eye"></i></a>';
                    return [
                        'nama_jenis_izin' => $permohonan->nama_jenis_izin,
                        'nomor_registrasi' => $permohonan->nomor_registrasi,
                        'waktu_pengajuan' => $permohonan->created_at->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
                        'nama_pemohon' => $permohonan->nama,
                        // 'surat_permohonan_rekomendasi' => $permohonan->surat_permohonan_rekomendasi_filepath,
                        'surat_rekomendasi' => $permohonan->surat_rekomendasi_filepath,
                        'status_badge' => $permohonan->status_badge_merge_usulan_baru_to_verifikasi,
                        'action' => $action,
                    ];
                })->values();

            // JSON response
            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalFiltered,
                'data' => $records,
            ]);
        }
    }

    public function verifikasiTable(Request $request, VerifikatorService $verifikatorService, BerkasPermohonanService $berkasPermohonanService)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = Permohonan::with(['alurPermohonan'])
                ->whereIn('status', [
                    StatusPermohonanEnum::PERMOHONAN_BARU->value,
                    StatusPermohonanEnum::VERIFIKASI->value,
                    StatusPermohonanEnum::VERIFIKASI_ULANG->value
                ])
                ->whereNotIn('status', [
                    StatusPermohonanEnum::PENDING->value
                ]);

            // order table
            if ($request->input('order.0.name') == 'waktu_pengajuan') {
                $query = $query->orderBy('created_at', $request->input('order.0.dir'));
            }

            // Total records
            $totalRecords = $query->count();

            // Filter records
            if ($search || $request->input('status') || $request->input('jenis_izin_id')) {
                if ($search) {
                    $query = $query->where('nomor_registrasi', 'like', '%' . $search . '%')
                        ->orWhere('nama', 'like', '%' . $search . '%');
                }
                if ($request->input('jenis_izin_id')) {
                    $query = $query->where('jenis_izin_id', $request->input('jenis_izin_id'));
                }
            }

            // Get data
            $records = $query
                ->get()
                ->filter(function ($permohonan) use ($verifikatorService) {
                    return $verifikatorService->isVerifikatorTurn($permohonan, auth()->user());
                })
                ->map(function ($permohonan) use ($berkasPermohonanService, $verifikatorService) {
                    $action = '<a href="' . route('verifikator.verifikasi.show', $permohonan->id) . '"><i class="isax-bold isax-eye"></i></a>';
                    return [
                        'nama_jenis_izin' => $permohonan->nama_jenis_izin,
                        'nomor_registrasi' => $permohonan->nomor_registrasi,
                        'waktu_pengajuan' => $permohonan->created_at->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
                        'nama_pemohon' => $permohonan->nama,
                        // 'surat_permohonan_rekomendasi' => $permohonan->surat_permohonan_rekomendasi_filepath,
                        'surat_rekomendasi' => $permohonan->surat_rekomendasi_filepath,
                        'status_berkas' => $berkasPermohonanService->isAllBerkasValidFromVerifikator($permohonan->alurPermohonan->first()),
                        'status_badge' => $permohonan->status_badge,
                        'action' => $action,
                    ];
                });


            // Total records
            $totalRecords = $records->count();

            // offset and limit
            $records = $records
                ->slice($start, $length)
                ->values();

            // JSON response
            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $records,
            ]);
        }
    }

    // AJAX
    public function validForm(Request $request, VerifikatorService $verifikatorService, FormPermohonanService $formPermohonanService)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $permohonan = Permohonan::find(decrypt($request->id));
        if (!$permohonan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permohonan tidak ditemukan',
            ], 404);
        }

        $is_verifikator_turn = $verifikatorService->isVerifikatorTurn($permohonan, auth()->user());

        if (!$is_verifikator_turn) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bukan giliran anda untuk melakukan validasi',
            ], 403);
        }

        $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($permohonan, auth()->user());

        try {
            $formPermohonanService->validFormByVerifikator($alur_permohonan);
        } catch (ServiceException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            Log::error($e->getFile() . $e->getLine() . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Form berhasil divalidasi',
        ]);
    }

    public function revisiForm(Request $request, VerifikatorService $verifikatorService, FormPermohonanService $formPermohonanService)
    {
        $request->validate([
            'id' => 'required',
            'catatan_revisi' => 'required',
        ]);

        $permohonan = Permohonan::find(decrypt($request->id));
        if (!$permohonan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permohonan tidak ditemukan',
            ], 404);
        }

        $is_verifikator_turn = $verifikatorService->isVerifikatorTurn($permohonan, auth()->user());
        if (!$is_verifikator_turn) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bukan giliran anda untuk melakukan revisi',
            ], 403);
        }

        $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($permohonan, auth()->user());

        try {
            $formPermohonanService->revisiFormByVerifikator($alur_permohonan, $request->catatan_revisi);
        } catch (ServiceException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            Log::error($e->getFile() . $e->getLine() . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Form berhasil direvisi',
        ]);
    }

    public function validBerkas(Request $request, VerifikatorService $verifikatorService, BerkasPermohonanService $berkasPermohonanService)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $berkas_permohonan = BerkasPermohonan::with('permohonan')->find(decrypt($request->id));
        if (!$berkas_permohonan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Berkas tidak ditemukan',
            ], 404);
        }

        $is_verifikator_turn = $verifikatorService->isVerifikatorTurn($berkas_permohonan->permohonan, auth()->user());

        if (!$is_verifikator_turn) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bukan giliran anda untuk melakukan validasi',
            ], 403);
        }

        try {
            $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($berkas_permohonan->permohonan, auth()->user());

            // validasi berkas
            $berkasPermohonanService->validBerkasByVerifikator($alur_permohonan, $berkas_permohonan);
        } catch (ServiceException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            Log::error($e->getFile() . $e->getLine() . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berkas berhasil divalidasi',
        ]);
    }

    public function revisiBerkas(Request $request, VerifikatorService $verifikatorService, BerkasPermohonanService $berkasPermohonanService)
    {
        $request->validate([
            'id' => 'required',
            'catatan_revisi' => 'required',
        ]);

        $berkas_permohonan = BerkasPermohonan::with('permohonan')->find(decrypt($request->id));
        if (!$berkas_permohonan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Berkas tidak ditemukan',
            ], 404);
        }

        $is_verifikator_turn = $verifikatorService->isVerifikatorTurn($berkas_permohonan->permohonan, auth()->user());
        if (!$is_verifikator_turn) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bukan giliran anda untuk melakukan revisi',
            ], 403);
        }

        try {
            $alur_permohonan = $verifikatorService->getAlurPermohonanByVerifikator($berkas_permohonan->permohonan, auth()->user());

            // revisi berkas
            $berkasPermohonanService->revisiBerkasByVerifikator($alur_permohonan, $berkas_permohonan, $request->catatan_revisi);
        } catch (ServiceException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            Log::error($e->getFile() . $e->getLine() . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berkas berhasil direvisi',
        ]);
    }
}
