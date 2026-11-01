<?php

namespace App\Http\Controllers;

use App\Models\KategoriIzin;
use Illuminate\Http\Request;

class KategoriIzinController extends Controller
{
    public function index()
    {
        return view('pages.admin.master-data.kategori-izin.index');
    }

    public function create()
    {
        return view('pages.admin.master-data.kategori-izin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        KategoriIzin::create($request->only('nama'));
        return redirect()->route('admin.master-data.kategori-izin.index')->with('success', 'Kategori Izin berhasil ditambahkan');
    }

    public function show(Request $request)
    {
        $kategori_izin = KategoriIzin::findOrFail($request->id);
        return view('pages.admin.master-data.kategori-izin.show', compact('kategori_izin'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        $kategori_izin = KategoriIzin::findOrFail($id);
        $kategori_izin->update($request->only('nama'));
        return redirect()->route('admin.master-data.kategori-izin.index')->with('success', 'Kategori Izin berhasil diubah');
    }

    public function kategoriIzinTable(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = KategoriIzin::with('sektorIzin');

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
                ->map(function ($kategori_izin) {
                    // $action = '<a href="' . route('admin.jenis-izin.show', $kategori_izin->id) . '" class="btn btn-sm btn-primary"><i class="isax isax-trash"></i></a>';
                    $action = '<a href="' . route('admin.master-data.kategori-izin.show', $kategori_izin->id) . '"><i class="isax-bold isax-brush-1"></i></a>';
                    // $action .= '<a href="#"><i class="isax-bold isax-trash"></i></a>';
                    return [
                        'id' => $kategori_izin->id,
                        'nama' => $kategori_izin->nama,
                        'created_at' => $kategori_izin->created_at->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
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
