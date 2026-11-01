<?php

namespace App\Http\Controllers;

use App\Models\GroupLayananSkm;
use App\Models\LayananSkm;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KuesionerPertanyaan;
use Illuminate\Support\Facades\Log;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $surveys = KuesionerPertanyaan::with('groupLayananSkm')
            ->whereNotNull('group_layanan_skm_id')
            ->get()
            ->groupBy(function ($item) {
                return $item->groupLayananSkm->nama;
            })
            ->map(function ($item) {
                return [
                    'id' => $item->first()->groupLayananSkm->id,
                    'total_pertanyaan' => $item->count(),
                ];
            });

        return view('pages.admin.master-data.survey.index', compact('surveys'));
    }

    public function create(Request $request)
    {
        $layananSkm = GroupLayananSkm::all();
        return view('pages.admin.master-data.survey.create', compact('layananSkm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_layanan_skm_id' => 'required',
            'pertanyaan' => 'required',
            'pertanyaan.*.pertanyaan' => 'required',
            'pertanyaan.*.pilihan_jawaban_1' => 'required',
            'pertanyaan.*.pilihan_jawaban_2' => 'required',
            'pertanyaan.*.pilihan_jawaban_3' => 'required',
            'pertanyaan.*.pilihan_jawaban_4' => 'required',
        ]);

        $layanan_skm = GroupLayananSkm::find($request->group_layanan_skm_id);

        if (!$layanan_skm) {
            return redirect()->back()->with('error', 'Layanan SKM tidak ditemukan');
        }

        $kuesionerPertanyaan = KuesionerPertanyaan::where('group_layanan_skm_id', $layanan_skm->id)
            ->first();

        if ($kuesionerPertanyaan) {
            return redirect()->back()->with('error', 'Group layanan ini telah terdapat dalam survey khusus. Silahkan gunakan fitur edit untuk mengubah data')->withInput();
        }

        DB::beginTransaction();
        try {
            $state = Str::random(10);

            // delete all pertanyaan
            KuesionerPertanyaan::where('group_layanan_skm_id', $layanan_skm->id)->delete();

            foreach ($request->pertanyaan as $pertanyaan) {
                $kuesionerPertanyaan = KuesionerPertanyaan::create([
                    'group_layanan_skm_id' => $layanan_skm->id,
                    'pertanyaan' => $pertanyaan['pertanyaan'],
                    'state' => $state,
                ]);

                $kuesionerPertanyaan->kuesionerOpsi()->create([
                    'kuesioner_pertanyaan_id' => $kuesionerPertanyaan->id,
                    'opsi' => $pertanyaan['pilihan_jawaban_1'],
                    'point' => 1,
                ]);

                $kuesionerPertanyaan->kuesionerOpsi()->create([
                    'kuesioner_pertanyaan_id' => $kuesionerPertanyaan->id,
                    'opsi' => $pertanyaan['pilihan_jawaban_2'],
                    'point' => 2,
                ]);

                $kuesionerPertanyaan->kuesionerOpsi()->create([
                    'kuesioner_pertanyaan_id' => $kuesionerPertanyaan->id,
                    'opsi' => $pertanyaan['pilihan_jawaban_3'],
                    'point' => 3,
                ]);

                $kuesionerPertanyaan->kuesionerOpsi()->create([
                    'kuesioner_pertanyaan_id' => $kuesionerPertanyaan->id,
                    'opsi' => $pertanyaan['pilihan_jawaban_4'],
                    'point' => 4,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }

        return redirect()->route('admin.master-data.survey.index')->with('success', 'Berhasil menyimpan data');
    }

    public function show(Request $request, $id)
    {
        $group_layanan_skm = GroupLayananSkm::withWhereHas('kuesionerPertanyaan')->findOrfail($id);
        $group_layanan_skm->load(['kuesionerPertanyaan.kuesionerOpsi' => function ($query) {
            $query->orderBy('point', 'asc');
        }]);
        $layananSkm = GroupLayananSkm::all();
        return view('pages.admin.master-data.survey.show', compact(
            'group_layanan_skm',
            'layananSkm'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'group_layanan_skm_id' => 'required|exists:group_layanan_skms,id|exists:kuesioner_pertanyaans,group_layanan_skm_id',
            'pertanyaan' => 'required',
            'pertanyaan.*.pertanyaan' => 'required',
            'pertanyaan.*.pilihan_jawaban_1' => 'required',
            'pertanyaan.*.pilihan_jawaban_2' => 'required',
            'pertanyaan.*.pilihan_jawaban_3' => 'required',
            'pertanyaan.*.pilihan_jawaban_4' => 'required',
        ]);

        $layanan_skm = GroupLayananSkm::find($request->group_layanan_skm_id);

        if (!$layanan_skm) {
            return redirect()->back()->with('error', 'Layanan SKM tidak ditemukan');
        }

        // check if kuesioner jawaban in kuesioner pertanyaan this group layanan skm is exist
        $kuesionerPertanyaan = KuesionerPertanyaan::where('group_layanan_skm_id', $layanan_skm->id)
            ->whereHas('kuesionerJawaban')
            ->first();

        // if ($kuesionerPertanyaan) {
        //     return redirect()->back()->with('error', 'Data tidak dapat diubah karena sudah ada data survey yang terisi');
        // }

        DB::beginTransaction();
        try {
            $state = Str::random(10);

            // delete all pertanyaan
            KuesionerPertanyaan::where('group_layanan_skm_id', $layanan_skm->id)->delete();

            foreach ($request->pertanyaan as $pertanyaan) {
                $kuesionerPertanyaan = KuesionerPertanyaan::create([
                    'group_layanan_skm_id' => $layanan_skm->id,
                    'pertanyaan' => $pertanyaan['pertanyaan'],
                    'state' => $state,
                ]);

                $kuesionerPertanyaan->kuesionerOpsi()->create([
                    'kuesioner_pertanyaan_id' => $kuesionerPertanyaan->id,
                    'opsi' => $pertanyaan['pilihan_jawaban_1'],
                    'point' => 1,
                ]);

                $kuesionerPertanyaan->kuesionerOpsi()->create([
                    'kuesioner_pertanyaan_id' => $kuesionerPertanyaan->id,
                    'opsi' => $pertanyaan['pilihan_jawaban_2'],
                    'point' => 2,
                ]);

                $kuesionerPertanyaan->kuesionerOpsi()->create([
                    'kuesioner_pertanyaan_id' => $kuesionerPertanyaan->id,
                    'opsi' => $pertanyaan['pilihan_jawaban_3'],
                    'point' => 3,
                ]);

                $kuesionerPertanyaan->kuesionerOpsi()->create([
                    'kuesioner_pertanyaan_id' => $kuesionerPertanyaan->id,
                    'opsi' => $pertanyaan['pilihan_jawaban_4'],
                    'point' => 4,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }

        return redirect()->route('admin.master-data.survey.index')->with('success', 'Berhasil menyimpan data');
    }
}
