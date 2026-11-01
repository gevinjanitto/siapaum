<?php

namespace App\Http\Controllers;

use App\Enums\StatusPermohonanEnum;
use Carbon\Carbon;
use App\Models\Reklame;
use Illuminate\Http\Request;
use App\Models\FormJenisIzin;
use App\Models\RegistrasiReklame;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\FormReklameRequest;
use App\Http\Requests\FormReklameEditRequest;
use App\Http\Requests\RegistrasiReklameRequest;
use App\Models\JenisIzin;

class UserReklameController extends Controller
{
    public function index()
    {
        $registrasi_reklame = RegistrasiReklame::where('user_id', auth()->id())->get();
        return view('pages.public.reklame.index', compact('registrasi_reklame'));
    }

    public function search(Request $request)
    {
        return view('pages.public.reklame.search');
    }

    public function create(Request $request)
    {
        $registrasi_reklame = RegistrasiReklame::with(['reklame' => function ($query) {
            $query->whereNull('permohonan_id')->with('formReklame');
        }])
            ->where('nomor_registrasi', $request->nomor_registrasi)->first();
        if ($registrasi_reklame) {
            return view('pages.public.reklame.create', compact('registrasi_reklame'));
        } else {
            return redirect()->back()->with('error', 'Nomor Registrasi tidak ditemukan')->withInput();
        }
    }

    public function store(Request $request, $nomor_registrasi)
    {
        $registrasi_reklame = RegistrasiReklame::where('nomor_registrasi', decrypt($nomor_registrasi))->first();
        if (!$registrasi_reklame) {
            return redirect()->back()->with('error', 'Nomor Registrasi tidak ditemukan')->withInput();
        }

        $registrasi_reklame->load(['reklame' => function ($query) {
            $query->whereNull('permohonan_id')->with('formReklame');
        }]);

        if ($registrasi_reklame->reklame->count() == 0) {
            return redirect()->back()->with('error', 'Belum terdapat data reklame pada nomor registrasi ini');
        }

        $jenis_izin = JenisIzin::with([
            'alurJenisIzin',
            'formJenisIzin',
            'berkasJenisIzin',
            'kelengkapanJenisIzin',
        ])->find(9);

        DB::beginTransaction();
        try {
            foreach ($registrasi_reklame->reklame as $reklame) {
                // create permohonan
                $permohonan = $reklame->permohonan()->create([
                    'user_id' => auth()->id(),
                    'jenis_izin_id' => $jenis_izin->id,
                    'nama_jenis_izin' => $jenis_izin->nama,
                    'deskripsi_jenis_izin' => $jenis_izin->deskripsi,
                    'nomor_registrasi' => $registrasi_reklame->nomor_registrasi,
                    'nama' => $registrasi_reklame->nama,
                    'nik' => $registrasi_reklame->nik,
                    'npwp' => $registrasi_reklame->npwp,
                    'tempat_lahir' => '',
                    'status' => StatusPermohonanEnum::PENDING->value,
                ]);

                // create form reklame
                foreach ($reklame->formReklame as $form) {
                    $permohonan->formPermohonan()->create([
                        'kode_isian' => $form->kode_isian,
                        'tipe' => $form->tipe,
                        'label' => $form->label,
                        'value' => $form->value,
                        'urutan' => $form->urutan,
                    ]);
                }

                // alur permohonan
                foreach ($jenis_izin->alurJenisIzin as $alur) {
                    $permohonan->alurPermohonan()->create([
                        'verifikator_id' => $alur->verifikator_id,
                        'jenis_verifikator' => $alur->jenis_verifikator,
                        'urutan' => $alur->urutan,
                        'is_done' => 0
                    ]);
                }

                // berkas permohonan
                foreach ($jenis_izin->berkasJenisIzin as $berkas) {
                    $permohonan->berkasPermohonan()->create([
                        'nama' => $berkas->nama,
                        'is_required' => $berkas->is_required,
                        'urutan' => $berkas->urutan,
                        'is_valid' => 0,
                    ]);
                }

                // kelengkapan permohonan
                foreach ($jenis_izin->kelengkapanJenisIzin as $kelengkapan) {
                    $permohonan->kelengkapanPermohonan()->create([
                        'label' => $kelengkapan->label,
                        'tipe' => $kelengkapan->tipe,
                        'kode_isian' => $kelengkapan->kode_isian,
                        'urutan' => $kelengkapan->urutan,
                    ]);
                }

                $reklame->update([
                    'permohonan_id' => $permohonan->id,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getFile() . $e->getLine() . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server')->withInput();
        }

        return redirect()->route('dashboard')->with('success', 'Data Reklame berhasil ditambahkan dengan status pending. Silahkan lengkapi berkas permohonan pada setiap reklame');
    }

    public function registrasi(Request $request)
    {
        return view('pages.public.reklame.registrasi');
    }

    public function destroy(Request $request, $nomor_registrasi)
    {
        $registrasi_reklame = RegistrasiReklame::with(['reklame' => function ($query) {
            $query->whereNull('permohonan_id');
        }])
            ->where('nomor_registrasi', decrypt($nomor_registrasi))->firstOrFail();
        if ($registrasi_reklame->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Data Registrasi ini bukan milik anda');
        }

        if ($registrasi_reklame->reklame->count() > 0) {
            return redirect()->back()->with('error', 'Data Registrasi ini masih memiliki data reklame');
        }

        $registrasi_reklame->delete();

        return redirect()->back()->with('success', 'Data Registrasi Reklame berhasil dihapus');
    }

    public function storeRegistrasi(RegistrasiReklameRequest $request)
    {
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

        return redirect()->route('public.reklame.create', ['nomor_registrasi' => $reklame->nomor_registrasi])->with('success', 'Data Registrasi Reklame berhasil disimpan');
    }

    public function createReklame(Request $request, $registrasi_reklame)
    {
        $registrasi_reklame = RegistrasiReklame::with(['reklame' => function ($query) {
            $query->with('formReklame')->whereNull('permohonan_id');
        }])
            ->where('nomor_registrasi', decrypt($registrasi_reklame))->first();
        if (!$registrasi_reklame) {
            return redirect()->back()->with('error', 'Nomor Registrasi tidak ditemukan')->withInput();
        }

        $form_jenis_izin = FormJenisIzin::where('jenis_izin_id', 9)
            ->whereNotIn('kode_isian', ['NAMA_PERUSAHAAN', 'HP/TELP', 'ALAMAT'])
            ->get();

        return view('pages.public.reklame.insert', compact('registrasi_reklame', 'form_jenis_izin'));
    }

    public function storeReklame(FormReklameRequest $request, $registrasi_reklame)
    {
        $registrasi_reklame = RegistrasiReklame::with('reklame.formReklame')->where('nomor_registrasi', decrypt($registrasi_reklame))->first();
        if (!$registrasi_reklame) {
            return redirect()->back()->with('error', 'Nomor Registrasi tidak ditemukan')->withInput();
        }

        DB::beginTransaction();
        try {
            $filepath = $request->file('image')->store('public/reklame-images');

            $reklame = $registrasi_reklame->reklame()->create([
                'image_filepath' => $filepath,
                'is_from_sireko' => false,
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

                if ($izin->kode_isian == 'AREA_PEMASANGAN') {
                    $reklame->formReklame()->create([
                        'kode_isian' => $izin->kode_isian,
                        'tipe' => $izin->tipe,
                        'label' => $izin->label,
                        'value' => $request->input($izin->kode_isian),
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
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getFile() . $e->getLine() . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server')->withInput();
        }
        return redirect()->route('public.reklame.create', ['nomor_registrasi' => $registrasi_reklame->nomor_registrasi])->with('success', 'Data Reklame berhasil disimpan');
    }

    public function editReklame(Request $request, $nomor_registrasi, Reklame $reklame)
    {
        $registrasi_reklame = RegistrasiReklame::where('nomor_registrasi', decrypt($nomor_registrasi))->firstOrFail();
        if ($reklame->is_from_sireko) {
            return redirect()->back()->with('error', 'Data Reklame ini berasal dari Sireko, tidak dapat diubah');
        }

        if ($reklame->permohonan_id) {
            return redirect()->back()->with('error', 'Data Reklame ini sudah diajukan, tidak dapat diubah');
        }

        if ($registrasi_reklame->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Data Reklame ini bukan milik anda');
        }

        $form_jenis_izin = FormJenisIzin::where('jenis_izin_id', 9)
            ->whereNotIn('kode_isian', ['NAMA_PERUSAHAAN', 'HP/TELP', 'ALAMAT'])
            ->get();

        $reklame->load('formReklame');

        return view('pages.public.reklame.edit', compact(
            'reklame',
            'form_jenis_izin',
            'registrasi_reklame'
        ));
    }

    public function updateReklame(FormReklameEditRequest $request, $nomor_registrasi, Reklame $reklame)
    {
        $registrasi_reklame = RegistrasiReklame::where('nomor_registrasi', decrypt($nomor_registrasi))->firstOrFail();
        if ($reklame->is_from_sireko) {
            return redirect()->back()->with('error', 'Data Reklame ini berasal dari Sireko, tidak dapat diubah');
        }

        if ($reklame->permohonan_id) {
            return redirect()->back()->with('error', 'Data Reklame ini sudah diajukan, tidak dapat diubah');
        }

        if ($registrasi_reklame->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Data Reklame ini bukan milik anda');
        }

        DB::beginTransaction();
        try {
            $reklame->update([
                'image_filepath' => $request->file('image') ? $request->file('image')->store('public/reklame-images') : $reklame->image_filepath,
            ]);

            $jenis_izin = FormJenisIzin::where('jenis_izin_id', 9)
                ->get();

            foreach ($jenis_izin as $izin) {
                if ($izin->kode_isian == 'AREA_PEMASANGAN') {
                    $reklame->formReklame()->where('kode_isian', $izin->kode_isian)->update([
                        'value' => $request->input($izin->kode_isian),
                    ]);
                    continue;
                }

                if ($izin->kode_isian == 'LAMA_PEMASANGAN') {
                    $tanggal_awal = Carbon::createFromFormat('d-m-Y', $request->input('TGL_MULAI'));
                    $tanggal_akhir = Carbon::createFromFormat('d-m-Y', $request->input('TGL_AKHIR'));
                    $lama_pemasangan = $tanggal_awal->diffInDays($tanggal_akhir);
                    $reklame->formReklame()->where('kode_isian', $izin->kode_isian)->update([
                        'value' => $lama_pemasangan,
                    ]);
                    continue;
                }

                if (in_array($izin->kode_isian, ['NAMA_PERUSAHAAN', 'HP/TELP', 'ALAMAT'])) {
                    continue;
                }

                $reklame->formReklame()->where('kode_isian', $izin->kode_isian)->update([
                    'value' => $request->input($izin->kode_isian),
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getFile() . $e->getLine() . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server')->withInput();
        }
        return redirect()->route('public.reklame.create', ['nomor_registrasi' => $registrasi_reklame->nomor_registrasi])->with('success', 'Data Reklame berhasil diubah');
    }

    public function destroyReklame(Request $request, $nomor_registrasi, Reklame $reklame)
    {
        $registrasi_reklame = RegistrasiReklame::where('nomor_registrasi', decrypt($nomor_registrasi))->firstOrFail();
        if ($reklame->is_from_sireko) {
            return redirect()->back()->with('error', 'Data Reklame ini berasal dari Sireko, tidak dapat dihapus');
        }

        if ($reklame->permohonan_id) {
            return redirect()->back()->with('error', 'Data Reklame ini sudah diajukan, tidak dapat dihapus');
        }

        if ($registrasi_reklame->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Data Reklame ini bukan milik anda');
        }

        $reklame->formReklame()->delete();
        $reklame->delete();

        return redirect()->back()->with('success', 'Data Reklame berhasil dihapus');
    }
}
