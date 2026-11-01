<?php

namespace App\Http\Controllers;

use App\Models\JenisIzin;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\JenisVerifikatorEnum;
use App\Enums\StatusPermohonanEnum;
use App\Services\PermohonanService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Notifications\PembayaranPajakReklameToPemohonNotication;

class PermohonanController extends Controller
{
    public function index(Request $request)
    {
        $jenis_izins = JenisIzin::all();
        return view('pages.admin.permohonan.index', compact(
            'jenis_izins'
        ));
    }

    public function show(Request $request, $id, PermohonanService $permohonan_service,)
    {
        $permohonan = Permohonan::with([
            'jenisIzin',
            'user',
            'formPermohonan',
            'berkasPermohonan',
            'kelengkapanPermohonan'
        ])->findOrFail($id);

        $steps = $permohonan_service->getStepAlurPermohonan($permohonan, true);
        $form_permohonans = $permohonan_service->getListFormPermohonan($permohonan);
        $berkas_permohonans = $permohonan_service->getListBerkasPermohonan($permohonan);
        $kelengkapan_permohonans = $permohonan_service->getListKelengkapanPermohonan($permohonan);
        $can_generate_ulang_izin_terbit = $this->canGenerateUlangIzinTerbit($permohonan);

        return view('pages.admin.permohonan.show', compact(
            'permohonan',
            'steps',
            'form_permohonans',
            'berkas_permohonans',
            'kelengkapan_permohonans',
            'can_generate_ulang_izin_terbit'
        ));
    }

    public function edit(Request $request, $id, PermohonanService $permohonan_service)
    {
        $permohonan = Permohonan::with([
            'formPermohonan',
            'kelengkapanPermohonan',
            'berkasPermohonan',
        ])->findOrFail($id);

        if (!in_array($permohonan->status, [
            StatusPermohonanEnum::PERMOHONAN_BARU->value,
            StatusPermohonanEnum::VERIFIKASI->value,
            StatusPermohonanEnum::VERIFIKASI_ULANG->value,
        ])) {
            return back()->with('error', 'Permohonan tidak dapat diubah');
        }

        $steps = $permohonan_service->getStepAlurPermohonan($permohonan, true);
        $can_generate_ulang_izin_terbit = $this->canGenerateUlangIzinTerbit($permohonan);

        return view('pages.admin.permohonan.edit', compact(
            'permohonan',
            'steps',
            'can_generate_ulang_izin_terbit'
        ));
    }

    public function update(Request $request, $id, PermohonanService $permohonan_service)
    {
        $permohonan = Permohonan::findOrFail($id);

        if (!in_array($permohonan->status, [
            StatusPermohonanEnum::PERMOHONAN_BARU->value,
            StatusPermohonanEnum::VERIFIKASI->value,
            StatusPermohonanEnum::VERIFIKASI_ULANG->value,
        ])) {
            return back()->with('error', 'Permohonan tidak dapat diubah');
        }

        $request->validate([
            'nama' => 'required|string',
            'nik' => 'required|string',
            'npwp' => 'nullable|string',
            'tempat_lahir' => 'required|string',
            'pas_foto' => 'nullable|file|mimes:jpeg,jpg,png|max:2048',
        ]);

        foreach ($permohonan->formPermohonan as $form) {
            $request->validate([
                $form->kode_isian => 'required',
            ]);
        }

        try {
            $permohonan->update([
                'nama' => $request->nama,
                'nik' => $request->nik,
                'npwp' => $request->npwp,
                'tempat_lahir' => $request->tempat_lahir,
            ]);

            foreach ($permohonan->formPermohonan as $form) {
                $form->update([
                    'value' => $request->{$form->kode_isian},
                ]);
            }

            foreach ($permohonan->kelengkapanPermohonan as $kelengkapan) {
                $kelengkapan->update([
                    'value' => $request->{$kelengkapan->kode_isian},
                ]);
            }

            if ($request->hasFile('pas_foto')) {
                $old_pas_foto_filepath = $permohonan->pas_foto_filepath;
                $pas_foto_filepath = $request->file('pas_foto')->store('public/pas_foto');

                $permohonan->update([
                    'is_pas_foto_required' => 1,
                    'pas_foto_filepath' => $pas_foto_filepath,
                ]);

                if ($old_pas_foto_filepath) {
                    Storage::delete($old_pas_foto_filepath);
                }
            }

            return redirect()->back()->with('success', 'Permohonan berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function generateUlangIzinTerbit(Request $request, Permohonan $permohonan, PermohonanService $permohonan_service)
    {
        if (!$this->canGenerateUlangIzinTerbit($permohonan)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Izin terbit hanya dapat digenerate ulang setelah verifikator BO selesai dan sebelum permohonan selesai atau ditandatangani',
            ], 422);
        }

        try {
            $filepath = $permohonan_service->generateIzinTerbit($permohonan);
            $permohonan->template_surat_filepath = $filepath;
            $permohonan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Izin terbit berhasil digenerate ulang',
                'data' => [
                    'filepath' => Storage::url($filepath),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function canGenerateUlangIzinTerbit(Permohonan $permohonan): bool
    {
        return $permohonan->status != StatusPermohonanEnum::SELESAI->value
            && !$permohonan->is_ttd
            && $permohonan->alurPermohonan()
                ->where('jenis_verifikator', JenisVerifikatorEnum::BO->value)
                ->exists()
            && !$permohonan->alurPermohonan()
                ->where('jenis_verifikator', JenisVerifikatorEnum::BO->value)
                ->where('is_done', 0)
                ->exists();
    }

    public function permohonanTable(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = Permohonan::with([
                // 'jenisIzin',
                // 'user'
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
                    $query = $query->where('nomor_registrasi', 'like', '%' . $search . '%')
                        ->orWhere('nama', 'like', '%' . $search . '%');
                }
                if ($request->input('status')) {
                    $query = $query->where('status', $request->input('status'));
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
                    $action = '';

                    if (in_array($permohonan->status, [
                        StatusPermohonanEnum::PERMOHONAN_BARU->value,
                        StatusPermohonanEnum::VERIFIKASI->value,
                        StatusPermohonanEnum::VERIFIKASI_ULANG->value,
                    ])) {
                        $action .= '<a href="' . route('admin.permohonan.edit', $permohonan->id) . '"><i class="isax-bold isax-edit me-2"></i></a>';
                    }
                    // $action = '<a href="' . route('admin.jenis-izin.show', $permohonan->id) . '" class="btn btn-sm btn-primary"><i class="isax isax-trash"></i></a>';
                    $action .= '<a href="' . route('admin.permohonan.show', $permohonan->id) . '"><i class="isax-bold isax-eye"></i></a>';
                    $action .= '<button data-id="' . $permohonan->id . '" class="btn btn-delete-permohonan"><i class="isax-bold isax-trash"></i></button>';
                    return [
                        'nama_jenis_izin' => $permohonan->nama_jenis_izin,
                        'nomor_registrasi' => $permohonan->nomor_registrasi,
                        'waktu_pengajuan' => $permohonan->created_at->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
                        'nama_pemohon' => $permohonan->nama,
                        // 'surat_permohonan_rekomendasi' => $permohonan->surat_permohonan_rekomendasi_filepath,
                        'surat_rekomendasi' => $permohonan->surat_rekomendasi_filepath,
                        'status_badge' => $permohonan->status_badge,
                        'action' => $action,
                    ];
                });

            // JSON response
            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalFiltered,
                'data' => $records,
            ]);
        }
    }

    public function downloadIzinTerbit(Request $request, $permohonan, $filepath, PermohonanService $permohonan_service)
    {
        try {
            $permohonan = Permohonan::findOrFail($permohonan);
            return $permohonan_service->downloadIzinTerbit($permohonan, auth()->user());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $permohonan = Permohonan::findOrFail($id);
            $permohonan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus permohonan'
            ], 500);
        }
    }

    public function storeBerkas(Request $request)
    {
        $request->validate([
            'berkas' => 'required|file|mimes:pdf',
            'berkas_key' => 'required',
        ]);

        // validate max berkas 5mb
        if ($request->file('berkas')->getSize() > 5000000) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran berkas maksimal 5MB'
            ]);
        }

        DB::beginTransaction();
        try {
            $permohonan = Permohonan::find($request->permohonan);
            $berkas_permohonan = $permohonan->berkasPermohonan()->with('validasiBerkas')->find($request->berkas_key);
            if (!$berkas_permohonan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode berkas yang anda upload tidak ditemukan'
                ]);
            }
            $berkas = $request->file('berkas');
            $berkas_path = $berkas->store('public/berkas_permohonan');
            $berkas_permohonan->update([
                'filepath' => $berkas_path
            ]);
            $berkas_permohonan->validasiBerkas()->where('status', 'revisi')->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('error')->error($e->getFile() . $e->getLine() . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kegagalan sistem, silahkan hubungi administrator'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Berkas berhasil diunggah'
        ]);
    }

    public function uploadSkpd(Request $request, Permohonan $permohonan)
    {
        $request->validate([
            'berkas_key' => 'required|in:skpd',
            'berkas' => 'required|file|mimes:pdf|max:2048',
        ]);

        if ($permohonan->jenis_izin_id != 9) {
            return response()->json([
                'success' => false,
                'message' => 'Izin ini tidak memerlukan SKPD',
            ]);
        }

        $permohonan->reklame->skpd_filepath = $request->file('berkas')->store('public/permohonan/reklame/skpd');
        $permohonan->reklame->save();

        $permohonan->user->notify(new PembayaranPajakReklameToPemohonNotication($permohonan));

        return response()->json([
            'success' => true,
            'message' => 'SKPD berhasil diunggah',
        ]);
    }

    public function uploadBuktiBayarReklame(Request $request, $permohonan)
    {
        $request->validate([
            // 'bukti_bayar' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'berkas_key' => 'required|in:bukti_bayar',
            'berkas' => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);

        // validate max berkas 5mb
        if ($request->file('berkas')->getSize() > 5000000) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran berkas maksimal 5MB'
            ]);
        }

        DB::beginTransaction();
        try {
            $permohonan = Permohonan::with('reklame')->find($permohonan);
            if (!$permohonan->reklame) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data reklame tidak ditemukan'
                ]);
            }
            $berkas = $request->file('berkas');
            $berkas_path = $berkas->store('public/bukti_bayar_reklame');
            $permohonan->reklame->update([
                'bukti_bayar_filepath' => $berkas_path
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('error')->error($e->getFile() . $e->getLine() . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kegagalan sistem, silahkan hubungi administrator'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Bukti bayar berhasil diunggah'
        ]);
    }
}
