<?php

namespace App\Http\Controllers;

use App\Models\LayananSkm;
use Illuminate\Http\Request;
use App\Models\GroupLayananSkm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LayananSkmController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.admin.master-data.layanan-skm.index');
    }

    public function create(Request $request)
    {
        $group_layanan_skms = GroupLayananSkm::all();
        return view('pages.admin.master-data.layanan-skm.create', compact('group_layanan_skms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'group_layanan_skm_id' => 'required|exists:group_layanan_skms,id',
        ]);

        try {
            $layananSkm = new LayananSkm();
            $layananSkm->nama = $request->nama;
            $layananSkm->group_layanan_skm_id = $request->group_layanan_skm_id;
            $layananSkm->save();

            return redirect()->route('admin.master-data.layanan-skm.index')->with('success', 'Berhasil menambahkan layanan skm');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal menambahkan layanan skm');
        }
    }

    public function show(Request $request, $id)
    {
        $layanan_skm = LayananSkm::findOrFail($id);
        $group_layanan_skms = GroupLayananSkm::all();
        return view('pages.admin.master-data.layanan-skm.show', compact('layanan_skm', 'group_layanan_skms'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string',
            'group_layanan_skm_id' => 'required|exists:group_layanan_skms,id',
        ]);

        try {
            $layananSkm = LayananSkm::findOrFail($id);
            $layananSkm->nama = $request->nama;
            $layananSkm->group_layanan_skm_id = $request->group_layanan_skm_id;
            $layananSkm->save();

            return redirect()->route('admin.master-data.layanan-skm.index')->with('success', 'Berhasil mengubah layanan skm');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal mengubah layanan skm');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $layananSkm = LayananSkm::findOrFail($id);
            $layananSkm->delete();

            return redirect()->route('admin.master-data.layanan-skm.index')->with('success', 'Berhasil menghapus layanan skm');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal menghapus layanan skm');
        }
    }

    // Group Layanan SKM
    public function createGroupSkm(Request $request)
    {
        return view('pages.admin.master-data.layanan-skm.create-group-skm');
    }

    public function storeGroupSkm(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('group_layanan_skm', 'public');
            }

            $groupLayananSkm = new GroupLayananSkm();
            $groupLayananSkm->nama = $request->nama;
            $groupLayananSkm->image_filepath = $imagePath ?? null;
            $groupLayananSkm->save();

            return redirect()->route('admin.master-data.layanan-skm.index')->with('success', 'Berhasil menambahkan group layanan skm');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal menambahkan group layanan skm');
        }
    }

    public function showGroupSkm(Request $request, $id)
    {
        $group_layanan_skm = GroupLayananSkm::findOrFail($id);
        return view('pages.admin.master-data.layanan-skm.show-group-skm', compact('group_layanan_skm'));
    }

    public function updateGroupSkm(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('group_layanan_skm', 'public');
            }

            $groupLayananSkm = GroupLayananSkm::findOrFail($id);
            $groupLayananSkm->nama = $request->nama;
            $groupLayananSkm->image_filepath = $imagePath ?? null;
            $groupLayananSkm->save();

            return redirect()->route('admin.master-data.layanan-skm.index')->with('success', 'Berhasil mengubah group layanan skm');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal mengubah group layanan skm');
        }
    }

    public function layananSkmTable(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = LayananSkm::with('groupLayananSkm');

            // Total records
            $totalRecords = $query->count();

            // Search
            if ($search) {
                $query = $query->where('nama', 'like', '%' . $search . '%');
                $totalFiltered = $query->count();
            } else {
                $totalFiltered = $query->count();
            }

            // Offset and limit
            if ($start != 0 || $length != -1) {
                $query = $query->offset($start)
                    ->limit($length);
            }

            // Get data
            $records = $query
                ->get()
                ->map(function ($layanan_skm) {
                    // $action = '<a href="' . route('admin.jenis-izin.show', $layanan_skm->id) . '" class="btn btn-sm btn-primary"><i class="isax isax-trash"></i></a>';
                    $action = '<a href="' . route('admin.master-data.layanan-skm.show', $layanan_skm->id) . '"><i class="isax-bold isax-brush-1"></i></a>';
                    // $action .= '<a href="#"><i class="isax-bold isax-trash"></i></a>';
                    return [
                        'id' => $layanan_skm->id,
                        'nama' => $layanan_skm->nama,
                        'nama_group' => $layanan_skm->groupLayananSkm->nama,
                        'created_at' => $layanan_skm->created_at->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
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

    public function groupSkmTable(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = GroupLayananSkm::query();

            // Total records
            $totalRecords = $query->count();

            // Search
            if ($search) {
                $query = $query->where('nama', 'like', '%' . $search . '%');
                $totalFiltered = $query->count();
            } else {
                $totalFiltered = $query->count();
            }

            // Offset and limit
            if ($start != 0 || $length != -1) {
                $query = $query->offset($start)
                    ->limit($length);
            }

            // Get data
            $records = $query
                ->get()
                ->map(function ($group_layanan_skm) {
                    $action = '<a href="' . route('admin.master-data.layanan-skm.group-skm.show', $group_layanan_skm->id) . '"><i class="isax-bold isax-brush-1"></i></a>';
                    return [
                        'id' => $group_layanan_skm->id,
                        'nama' => $group_layanan_skm->nama,
                        'image_url' => $group_layanan_skm->image_url,
                        'created_at' => $group_layanan_skm->created_at->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
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
