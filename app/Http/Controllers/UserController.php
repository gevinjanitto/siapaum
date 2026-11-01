<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use App\Enums\JenisKelaminEnum;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.admin.master-data.user.index');
    }

    public function create(Request $request)
    {
        $roles = RoleEnum::arrayWithout(RoleEnum::PUBLIC->value);
        $jenis_kelamin = JenisKelaminEnum::array();
        return view('pages.admin.master-data.user.create', compact(
            'roles',
            'jenis_kelamin'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'telepon' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'nik' => 'required',
            'password' => 'required|string',
            'role_id' => 'required|exists:roles,id|not_in:' . RoleEnum::PUBLIC->value,
        ]);

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role_id = $request->role_id;
            $user->telepon = $request->telepon;
            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->alamat = $request->alamat;
            $user->nik = $request->nik;
            $user->is_filled_data_register = true;
            $user->save();

            return redirect()->route('admin.master-data.user.index')->with('success', 'Berhasil menambahkan user');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal menambahkan user');
        }
    }

    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = RoleEnum::arrayWithout(RoleEnum::PUBLIC->value);
        $jenis_kelamin = JenisKelaminEnum::array();
        return view('pages.admin.master-data.user.show', compact(
            'user',
            'roles',
            'jenis_kelamin'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'telepon' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'nik' => 'required',
            'role_id' => 'required|exists:roles,id',
        ]);
        $user = User::findOrFail($id);

        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role_id = $request->role_id;
            $user->telepon = $request->telepon;
            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->alamat = $request->alamat;
            $user->nik = $request->nik;
            $user->save();

            return redirect()->route('admin.master-data.user.index')->with('success', 'Berhasil mengubah user');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal mengubah user');
        }
    }



    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string',
        ]);
        $user = User::findOrFail($id);

        try {
            $user->password = bcrypt($request->password);
            $user->save();

            return redirect()->route('admin.master-data.user.index')->with('success', 'Berhasil mengubah password user');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal mengubah password user');
        }
    }

    public function userTable(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = User::query();

            // Total records
            $totalRecords = $query->count();

            // Filter records
            if ($search) {
                $query = $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
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


            // Order By
            if ($request->input('order.0.name') == 'role') {
                $query = $query->orderBy('role_id', $request->input('order.0.dir'));
            }

            // Get data
            $records = $query
                ->get()
                ->map(function ($user) {
                    // $action = '<a href="' . route('admin.jenis-izin.show', $user->id) . '" class="btn btn-sm btn-primary"><i class="isax isax-trash"></i></a>';
                    $action = '<a href="' . route('admin.master-data.user.show', $user->id) . '"><i class="isax-bold isax-brush-1"></i></a>';
                    // $action .= '<a href="#"><i class="isax-bold isax-trash"></i></a>';
                    return [
                        'id' => $user->id,
                        'nama' => $user->name,
                        'email' => $user->email,
                        'nik' => $user->nik,
                        'nomor_telepon' => $user->telepon,
                        'role' => $user->role_badge,
                        'created_at' => $user->created_at->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
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
    public function getListVerifikator(Request $request)
    {
        $request->validate([
            'except' => 'array',
        ]);
        try {
            $query = User::where('role_id', RoleEnum::VERIFIKATOR->value)->where(function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%');
            });

            if (request()->except) {
                $query->whereNotIn('id', request()->except);
            }

            $verifikator = $query->paginate(10);
            $results = $verifikator->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name,
                ];
            });

            return response()->json([
                'results' => $results,
                'pagination' => [
                    'more' => $verifikator->hasMorePages()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data verifikator',
            ]);
        }
    }
}
