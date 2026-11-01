<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JenisIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\JenisVerifikatorEnum;
use App\Models\KategoriIzin;
use App\Models\SektorIzin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class JenisIzinController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.admin.master-data.jenis-izin.index');
    }

    public function create(Request $request)
    {
        $kategori_izins = KategoriIzin::with('sektorIzin')->get();
        return view('pages.admin.master-data.jenis-izin.create', compact('kategori_izins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:jenis_izins,nama',
            'sektor_izin_id' => 'required|exists:sektor_izins,id',
            'deskripsi' => 'required|string',
            'is_pas_foto_required' => 'required|in:0,1',
            'syarat_form' => 'required',
            'syarat_form.*.nama' => 'required',
            'syarat_form.*.kode_isian' => 'required',
            'syarat_form.*.tipe_form' => 'required|in:text,date',
            'syarat_berkas' => 'required',
            'syarat_berkas.*.nama' => 'required',
            'syarat_berkas.*.is_required' => 'required|in:0,1',
            'alur_verifikator' => 'required',
            'alur_verifikator.*.id' => 'required',
            'alur_verifikator.*.jenis_verifikator' => 'required|in:0,1,2,3,4',
            'syarat_kelengkapan' => 'required',
            'syarat_kelengkapan.*.nama' => 'required',
            'syarat_kelengkapan.*.kode_isian' => 'required',
            'syarat_kelengkapan.*.tipe_form' => 'required|in:text,date',
            'template_laporan' => 'required|mimes:doc,docx|max:4096',
        ]);

        // unique between kode isian from input user
        $all_kode_isian = array_merge(
            $request->syarat_form,
            $request->syarat_kelengkapan
        );

        // check if kode isian is unique
        $kode_isian = array_column($all_kode_isian, 'kode_isian');
        if (count($kode_isian) !== count(array_unique($kode_isian))) {
            return redirect()->back()->with('error', 'Kode isian pada syarat form dan kelengkapan harus unik')->withInput();
        }

        DB::beginTransaction();
        try {
            $jenis_izin = new JenisIzin();
            $sektor_izin = SektorIzin::with('kategoriIzin')->findOrFail($request->sektor_izin_id);
            $jenis_izin->sektor_izin_id = $request->sektor_izin_id;
            $jenis_izin->kategori_izin_id = $sektor_izin->kategoriIzin->id;
            $jenis_izin->is_pas_foto_required = $request->is_pas_foto_required;
            $jenis_izin->nama = $request->nama;
            $jenis_izin->deskripsi = $request->deskripsi;

            $file = $request->file('template_laporan');
            $path = $file->storeAs('public/file_templateword', $file->getClientOriginalName());

            $jenis_izin->template_surat = $path;

            $jenis_izin->save();

            // Form Jenis Izin
            foreach ($request->syarat_form as $key => $form) {
                $jenis_izin->formJenisIzin()->create([
                    'label' => $form['nama'],
                    'kode_isian' => $form['kode_isian'],
                    'tipe' => $form['tipe_form'],
                    'urutan' => $key + 1,
                ]);
            }

            // Berkas Jenis Izin
            foreach ($request->syarat_berkas as $key => $berkas) {
                $jenis_izin->berkasJenisIzin()->create([
                    'nama' => $berkas['nama'],
                    'is_required' => $berkas['is_required'],
                    'urutan' => $key + 1,
                ]);
            }

            // Alur Jenis Izin
            foreach ($request->alur_verifikator as $key => $verifikator) {
                $jenis_izin->alurJenisIzin()->create([
                    'verifikator_id' => $verifikator['id'],
                    'jenis_verifikator' => $verifikator['jenis_verifikator'],
                    'urutan' => $key + 1,
                ]);
            }

            // Kelengkapan Jenis Izin
            foreach ($request->syarat_kelengkapan as $key => $kelengkapan) {
                $jenis_izin->kelengkapanJenisIzin()->create([
                    'label' => $kelengkapan['nama'],
                    'kode_isian' => $kelengkapan['kode_isian'],
                    'tipe' => $kelengkapan['tipe_form'],
                    'urutan' => $key + 1,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.master-data.jenis-izin.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Kesalahan pada server. Hubungi Administrator')->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $jenis_izin = JenisIzin::with([
            'alurJenisIzin' => function ($q) {
                return $q->orderBy('urutan')->with('verifikator');
            },
            'formJenisIzin' => function ($q) {
                return $q->orderBy('urutan');
            },
            'berkasJenisIzin' => function ($q) {
                return $q->orderBy('urutan');
            },
            'kelengkapanJenisIzin' => function ($q) {
                return $q->orderBy('urutan');
            },
        ])->findOrFail($id);

        $kategori_izins = KategoriIzin::with('sektorIzin')->get();

        $jenis_izin->formJenisIzin = $jenis_izin->formJenisIzin->map(function ($formJenisIzin) {
            return collect([
                'id' => $formJenisIzin->id,
                'nama' => $formJenisIzin->label,
                'tipe_form' => $formJenisIzin->tipe,
                'kode_isian' => $formJenisIzin->kode_isian,
                'urutan' => $formJenisIzin->urutan,
            ]);
        });
        $jenis_izin->berkasJenisIzin = $jenis_izin->berkasJenisIzin->map(function ($berkasJenisIzin) {
            return collect([
                'id' => $berkasJenisIzin->id,
                'nama' => $berkasJenisIzin->nama,
                'is_required' => $berkasJenisIzin->is_required,
                'urutan' => $berkasJenisIzin->urutan,
            ]);
        });
        $jenis_izin->alurJenisIzin = $jenis_izin->alurJenisIzin->map(function ($alurJenisIzin) {
            return collect([
                'id' => $alurJenisIzin->verifikator->id,
                'nama' => $alurJenisIzin->verifikator->name,
                'jenis_verifikator' => $alurJenisIzin->jenis_verifikator,
                'jenis' => JenisVerifikatorEnum::tryFrom($alurJenisIzin->jenis_verifikator)->deskripsi(),
                'urutan' => $alurJenisIzin->urutan,
            ]);
        });
        $jenis_izin->kelengkapanJenisIzin = $jenis_izin->kelengkapanJenisIzin->map(function ($kelengkapanJenisIzin) {
            return collect([
                'id' => $kelengkapanJenisIzin->id,
                'nama' => $kelengkapanJenisIzin->label,
                'tipe_form' => $kelengkapanJenisIzin->tipe,
                'kode_isian' => $kelengkapanJenisIzin->kode_isian,
                'urutan' => $kelengkapanJenisIzin->urutan,
            ]);
        });

        return view('pages.admin.master-data.jenis-izin.show', compact('jenis_izin', 'kategori_izins'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string',
            'sektor_izin_id' => 'required|exists:sektor_izins,id',
            'is_pas_foto_required' => 'required|in:0,1',
            'deskripsi' => 'required|string',
            'syarat_form.*' => 'required',
            'syarat_form.*.nama' => 'required',
            'syarat_form.*.kode_isian' => 'required',
            'syarat_form.*.tipe_form' => 'required|in:text,date',
            'syarat_berkas.*' => 'required',
            'syarat_berkas.*.nama' => 'required',
            'syarat_berkas.*.is_required' => 'required|in:0,1',
            'alur_verifikator.*' => 'required',
            'alur_verifikator.*.id' => 'required',
            'alur_verifikator.*.jenis_verifikator' => 'required|in:0,1,2,3,4',
            'syarat_kelengkapan.*' => 'required',
            'syarat_kelengkapan.*.nama' => 'required',
            'syarat_kelengkapan.*.kode_isian' => 'required',
            'syarat_kelengkapan.*.tipe_form' => 'required|in:text,date',
        ]);

        if ($request->template_laporan) {
            $request->validate([
                'template_laporan' => 'required|mimes:doc,docx|max:4096',
            ]);
        }

        // unique between kode isian from input user
        $all_kode_isian = array_merge(
            $request->syarat_form,
            $request->syarat_kelengkapan
        );

        // check if kode isian is unique
        $kode_isian = array_column($all_kode_isian, 'kode_isian');
        if (count($kode_isian) !== count(array_unique($kode_isian))) {
            return redirect()->back()->with('error', 'Kode isian pada syarat form dan kelengkapan harus unik')->withInput();
        }


        $jenis_izin = JenisIzin::with([
            'alurJenisIzin' => function ($q) {
                return $q->orderBy('urutan')->with('verifikator');
            },
            'formJenisIzin' => function ($q) {
                return $q->orderBy('urutan');
            },
            'berkasJenisIzin' => function ($q) {
                return $q->orderBy('urutan');
            },
            'kelengkapanJenisIzin' => function ($q) {
                return $q->orderBy('urutan');
            },
        ])->findOrFail($id);


        DB::beginTransaction();
        try {
            $sektor_izin = SektorIzin::with('kategoriIzin')->findOrFail($request->sektor_izin_id);
            $jenis_izin->sektor_izin_id = $request->sektor_izin_id;
            $jenis_izin->nama = $request->nama;
            $jenis_izin->kategori_izin_id = $sektor_izin->kategoriIzin->id;
            $jenis_izin->is_pas_foto_required = $request->is_pas_foto_required;
            $jenis_izin->deskripsi = $request->deskripsi;

            if ($request->template_laporan) {
                Storage::disk('public')->delete($jenis_izin->template_surat);
                $file = $request->file('template_laporan');
                $path = $file->storeAs('public/file_templateword', $file->getClientOriginalName());

                $jenis_izin->template_surat = $path;
            }

            // Form Jenis Izin
            $jenis_izin->formJenisIzin()->delete();
            $urutan = 1;
            foreach ($request->syarat_form as $form) {
                $jenis_izin->formJenisIzin()->create([
                    'label' => $form['nama'],
                    'kode_isian' => $form['kode_isian'],
                    'tipe' => $form['tipe_form'],
                    'urutan' => $urutan++,
                ]);
            }

            // Berkas Jenis Izin
            $jenis_izin->berkasJenisIzin()->delete();
            $urutan = 1;
            foreach ($request->syarat_berkas as $berkas) {
                $jenis_izin->berkasJenisIzin()->create([
                    'nama' => $berkas['nama'],
                    'is_required' => $berkas['is_required'],
                    'urutan' => $urutan++,
                ]);
            }

            // Alur Jenis Izin
            $jenis_izin->alurJenisIzin()->delete();
            $urutan = 1;
            foreach ($request->alur_verifikator as $verifikator) {
                $jenis_izin->alurJenisIzin()->create([
                    'verifikator_id' => $verifikator['id'],
                    'jenis_verifikator' => $verifikator['jenis_verifikator'],
                    'urutan' => $urutan++,
                ]);
            }

            // Kelengkapan Jenis Izin
            $jenis_izin->kelengkapanJenisIzin()->delete();
            $urutan = 1;
            foreach ($request->syarat_kelengkapan as $kelengkapan) {
                $jenis_izin->kelengkapanJenisIzin()->create([
                    'label' => $kelengkapan['nama'],
                    'kode_isian' => $kelengkapan['kode_isian'],
                    'tipe' => $kelengkapan['tipe_form'],
                    'urutan' => $urutan++,
                ]);
            }

            $jenis_izin->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('error')->error($e->getFile() . $e->getLine() . $e->getMessage());
            return redirect()->back()->with('error', 'Kesalahan pada server. Hubungi Administrator')->withInput();
        }

        return redirect()->route('admin.master-data.jenis-izin.index')->with('success', 'Data berhasil diubah');
    }

    public function jenisIzinTable(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = JenisIzin::with('sektorIzin');

            // Total records
            $totalRecords = $query->count();

            // Filter records
            if ($search) {
                $query = $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', '%' . $search . '%');
                });

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
                ->map(function ($jenisIzin) {
                    // $action = '<a href="' . route('admin.jenis-izin.show', $jenisIzin->id) . '" class="btn btn-sm btn-primary"><i class="isax isax-trash"></i></a>';
                    $action = '<a href="' . route('admin.master-data.jenis-izin.show', $jenisIzin->id) . '"><i class="isax-bold isax-brush-1"></i></a>';
                    // $action .= '<a href="#"><i class="isax-bold isax-trash"></i></a>';
                    return [
                        'id' => $jenisIzin->id,
                        'nama' => $jenisIzin->nama,
                        'nama_kategori_izin' => $jenisIzin->sektorIzin?->kategoriIzin?->nama ?? '-',
                        'nama_sektor_izin' => $jenisIzin->sektorIzin?->nama ?? '-',
                        'created_at' => $jenisIzin->created_at->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
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
}
