<?php

namespace App\Http\Controllers;

use App\Models\JenisIzin;
use App\Models\Permohonan;
use App\Models\Tamu;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\GroupLayananSkm;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Laporan\BukuTamuExport;
use App\Exports\Laporan\IzinTerbitExport;
use App\Exports\SurveyLayanan\LaporanExport;
use App\Exports\Laporan\RekapPermohonanExport;

class LaporanController extends Controller
{
    public function ijinTerbitBulananIndex(Request $request)
    {
        return view('pages.admin.laporan.ijin-terbit-bulanan.index');
    }

    public function ijinTerbitBulananExport(Request $request)
    {
        $request->validate([
            'tahun' => 'required|numeric',
        ]);
        $tahun = $request->tahun;
        return Excel::download(new IzinTerbitExport($tahun), 'laporan_ijin_terbit_bulanan_' . $tahun . '.xlsx');
    }

    public function rekapPermohonanIndex(Request $request)
    {
        $jenis_izins = JenisIzin::all();
        return view('pages.admin.laporan.rekap-permohonan.index', compact('jenis_izins'));
    }

    public function rekapPermohonanVerifikatorIndex(Request $request)
    {
        $jenis_izin_id = Permohonan::whereHas('alurPermohonan', function($query) {
            $query->where('verifikator_id', auth()->user()->id);
        })->pluck('jenis_izin_id')->unique();

        $jenis_izins = JenisIzin::whereIn('id', $jenis_izin_id)->get();
        return view('pages.admin.laporan.rekap-permohonan.index', compact('jenis_izins'));
    }

    public function rekapPermohonanExport(Request $request)
    {
        $request->validate([
            'jenis_izin_id' => 'nullable|numeric|exists:jenis_izins,id',
            'periode' => 'required',
            'status' => 'nullable',
        ]);


        $jenis_izin_id = $request->jenis_izin_id;
        $periode = explode(' - ', $request->periode);
        $start = Carbon::createFromFormat('d/m/Y', $periode[0])->format('Y-m-d');
        $end = Carbon::createFromFormat('d/m/Y', $periode[1])->format('Y-m-d');

        $filename = 'laporan_rekap_permohonan_' . $start . '_' . $end . '.xlsx';

        $permohonans = Permohonan::with([
            'user',
            'kelengkapanPermohonan',
            'formPermohonan'
        ]);

        if ($jenis_izin_id) {
            $permohonans = $permohonans->where('jenis_izin_id', $jenis_izin_id);
        }

        if ($request->status) {
            $permohonans = $permohonans->where('status', $request->status);
        }

        $permohonans = $permohonans->whereBetween('created_at', [$start, $end])->get();

        return Excel::download(new RekapPermohonanExport($permohonans), $filename);
    }

    public function surveyLayananIndex(Request $request)
    {
        $groupLayananSkm = GroupLayananSkm::all();
        return view('pages.admin.laporan.survey-layanan.index', compact('groupLayananSkm'));
    }

    public function surveyLayananExport(Request $request)
    {
        $request->validate([
            'group_layanan_skm_id' => 'required|numeric',
            'periode' => 'required',
        ]);

        $groupLayananSkm = GroupLayananSkm::find($request->group_layanan_skm_id);
        $periode = explode(' - ', $request->periode);
        $start = Carbon::createFromFormat('d/m/Y', $periode[0])->format('Y-m-d');
        $end = Carbon::createFromFormat('d/m/Y', $periode[1])->format('Y-m-d');

        $namaLayanan = $groupLayananSkm?->nama ?? 'SIAP AUM';
        $filename = 'laporan_survey_layanan_' . $namaLayanan . '_' . $start . '_' . $end . '.xlsx';
        return Excel::download(new LaporanExport($groupLayananSkm, $start, $end), $filename);
    }

    public function bukuTamuIndex(Request $request)
    {
        $timezone = 'Asia/Makassar';
        $now = Carbon::now($timezone);
        $todayStart = $now->copy()->startOfDay()->timezone(config('app.timezone'));
        $todayEnd = $now->copy()->endOfDay()->timezone(config('app.timezone'));
        $last7DaysStart = $now->copy()->subDays(6)->startOfDay()->timezone(config('app.timezone'));
        $lastMonthStart = $now->copy()->subMonth()->startOfDay()->timezone(config('app.timezone'));

        $summary = [
            'today' => Tamu::whereBetween('created_at', [$todayStart, $todayEnd])->count(),
            'last_7_days' => Tamu::whereBetween('created_at', [$last7DaysStart, $todayEnd])->count(),
            'last_month' => Tamu::whereBetween('created_at', [$lastMonthStart, $todayEnd])->count(),
        ];

        return view('pages.admin.laporan.buku-tamu.index', compact('summary'));
    }

    public function bukuTamuTable(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            if (is_array($search)) {
                $search = $search['value'] ?? null;
            }

            $query = Tamu::query();
            $totalRecords = $query->count();

            if ($search) {
                $query = $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('telepon', 'like', '%' . $search . '%')
                        ->orWhere('alamat', 'like', '%' . $search . '%');
                });

                $totalFiltered = $query->count();
            } else {
                $totalFiltered = $totalRecords;
            }

            if ($request->input('order.0.name') == 'created_at') {
                $query = $query->orderBy('created_at', $request->input('order.0.dir'));
            } else {
                $query = $query->orderByDesc('created_at');
            }

            if ($start != 0 || $length != -1) {
                $query = $query->offset($start)
                    ->limit($length);
            }

            $records = $query
                ->get()
                ->map(function ($tamu) {
                    return [
                        'nama' => $tamu->nama,
                        'email' => $tamu->email ?? '-',
                        'telepon' => $tamu->telepon ?? '-',
                        'alamat' => $tamu->alamat ?? '-',
                        'created_at' => $tamu->created_at->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
                    ];
                });

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalFiltered,
                'data' => $records,
            ]);
        }
    }

    public function bukuTamuExport(Request $request)
    {
        $request->validate([
            'periode' => 'required|string',
        ]);

        $timezone = 'Asia/Makassar';
        $periode = explode(' - ', $request->periode);
        $start = Carbon::createFromFormat('d/m/Y', trim($periode[0]), $timezone)->startOfDay();
        $end = Carbon::createFromFormat('d/m/Y', trim($periode[1]), $timezone)->endOfDay();
        $queryStart = $start->copy()->timezone(config('app.timezone'));
        $queryEnd = $end->copy()->timezone(config('app.timezone'));

        $tamus = Tamu::whereBetween('created_at', [$queryStart, $queryEnd])
            ->orderByDesc('created_at')
            ->get();

        $filename = 'laporan_buku_tamu_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d') . '.xlsx';

        return Excel::download(new BukuTamuExport($tamus), $filename);
    }
}
