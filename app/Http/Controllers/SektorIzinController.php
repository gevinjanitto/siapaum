<?php

namespace App\Http\Controllers;

use App\Models\SektorIzin;
use App\Models\KategoriIzin;
use Illuminate\Http\Request;

class SektorIzinController extends Controller
{
    public function index()
    {
        return view('pages.admin.master-data.sektor-izin.index');
    }

    public function create()
    {
        $kategori_izins = KategoriIzin::all();
        return view('pages.admin.master-data.sektor-izin.create', compact('kategori_izins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_izin_id' => 'required',
            'nama' => 'required'
        ]);

        SektorIzin::create($request->only('kategori_izin_id', 'nama'));
        return redirect()->route('admin.master-data.sektor-izin.index')->with('success', 'Sektor Izin berhasil ditambahkan');
    }

    public function show(Request $request, $id)
    {
        $sektor_izin = SektorIzin::findOrFail($id);
        $kategori_izins = KategoriIzin::all();
        return view('pages.admin.master-data.sektor-izin.show', compact('sektor_izin', 'kategori_izins'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_izin_id' => 'required',
            'nama' => 'required'
        ]);

        $sektor_izin = SektorIzin::findOrFail($id);
        $sektor_izin->update($request->only('kategori_izin_id', 'nama'));
        return redirect()->route('admin.master-data.sektor-izin.index')->with('success', 'Sektor Izin berhasil diubah');
    }

    public function sektorIzinTable(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = SektorIzin::with('kategoriIzin');

            // Total records
            $totalRecords = $query->count();

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
            $data = $query
                ->get()
                ->map(function ($item) {
                    // $action = '<a href="' . route('admin.jenis-izin.show', $item->id) . '" class="btn btn-sm btn-primary"><i class="isax isax-trash"></i></a>';
                    $action = '<a href="' . route('admin.master-data.sektor-izin.show', $item->id) . '"><i class="isax-bold isax-brush-1"></i></a>';
                    // $action .= '<a href="#"><i class="isax-bold isax-trash"></i></a>';
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'nama_kategori_izin' => $item->kategoriIzin->nama,
                        'created_at' => $item->created_at->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
                        'action' => $action,
                    ];
                });

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalFiltered,
                'data' => $data
            ]);
        }
    }

}
