<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\StatusPermohonanEnum;
use App\Models\KuesionerPertanyaan;
use Illuminate\Support\Facades\Log;

class KuesionerController extends Controller
{
    public function create(Request $request, Permohonan $permohonan)
    {
        if ($permohonan->status != StatusPermohonanEnum::SELESAI->value) {
            return redirect()->back()->with('error', 'Permohonan belum selesai');
        }

        if ($permohonan->kuesioner()->exists()) {
            return redirect()->back()->with('error', 'Kuesioner sudah diisi');
        }

        $kuesioners = KuesionerPertanyaan::with('kuesionerOpsi')->whereNull('group_layanan_skm_id')->get();

        return view('pages.public.kuesioner.create', compact(
            'permohonan',
            'kuesioners'
        ));
    }

    public function store(Request $request, Permohonan $permohonan)
    {
        $request->validate([
            'pendidikan' => 'required',
            'pekerjaan' => 'required',
            'kuesioner' => 'required|array',
            'kuesioner.*' => 'required|integer',
        ]);

        $permohonan->load('user');

        // validate if all kuesioner id is inputted correctly as key in kuesioner request
        $kuesionerPertanyaans = KuesionerPertanyaan::with('kuesionerOpsi')->whereNull('group_layanan_skm_id')->get();
        $kuesionerIds = $kuesionerPertanyaans->pluck('id')->toArray();
        $diff = array_diff(array_keys($request->kuesioner), $kuesionerIds);
        if (!empty($diff)) {
            return redirect()->back()->with('error', 'Kuesioner tidak valid');
        }

        DB::beginTransaction();
        try {
            $kuesioner = $permohonan->kuesioner()->create([
                'nama' => $permohonan->user->name,
                'nomor_telepon' => $permohonan->user->telepon,
                'email' => $permohonan->user->email,
                'jenis_kelamin' => $permohonan->user->jenis_kelamin,
                'jenis_layanan' => $permohonan->jenisIzin->nama,
                'pendidikan' => $request->pendidikan,
                'pekerjaan' => $request->pekerjaan,
                'jenis_izin_id' => $permohonan->jenis_izin_id,
            ]);

            // validate value in kuesioner is belong to kuesioner opsi key
            foreach ($request->kuesioner as $kuesionerId => $kuesionerOpsiId) {
                $kuesionerPertanyaan = KuesionerPertanyaan::whereNull('group_layanan_skm_id')->find($kuesionerId);
                if (!$kuesionerPertanyaan->kuesionerOpsi->contains('id', $kuesionerOpsiId)) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Kuesioner tidak valid');
                }
            }

            $kuesioner->kuesionerJawaban()->createMany(
                array_map(function ($kuesionerId, $kuesionerOpsiId)  use ($kuesionerPertanyaans) {
                    return [
                        'kuesioner_pertanyaan_id' => $kuesionerId,
                        'kuesioner_opsi_id' => $kuesionerOpsiId,
                        'pertanyaan' => $kuesionerPertanyaans->where('id', $kuesionerId)->first()?->pertanyaan,
                        'opsi' => $kuesionerPertanyaans->where('id', $kuesionerId)->first()?->kuesionerOpsi->where('id', $kuesionerOpsiId)->first()?->opsi,
                        'point' => $kuesionerPertanyaans->where('id', $kuesionerId)->first()?->kuesionerOpsi->where('id', $kuesionerOpsiId)->first()?->point,
                    ];
                }, array_keys($request->kuesioner), $request->kuesioner)
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('error')->error($e->getFile() . $e->getLine() . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server')->withInput();
        }

        return redirect()->route('public.permohonan.show', $permohonan)->with('success', 'Kuesioner berhasil diisi');
    }
}
