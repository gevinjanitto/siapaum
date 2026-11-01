<?php

namespace App\Http\Controllers\OpenApi;

use App\Models\Permohonan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatistikRequest;
use App\Models\JenisIzin;
use Illuminate\Support\Facades\Validator;

class StatistikController extends Controller
{
    public function index(StatistikRequest $request)
    {
        $permohonan = Permohonan::with('jenisIzin');

        if ($request->status) {
            $permohonan->where('status', $request->status);
        }

        if ($request->tahun) {
            $permohonan->whereYear('pengajuan_at', $request->tahun);
        }

        if ($request->bulan) {
            $permohonan->whereMonth('pengajuan_at', $request->bulan);
        }

        $permohonan = $permohonan->get();
        $jenisIzin = JenisIzin::all();

        // Group permohonan by jenis_izin.nama
        $permohonanByIzin = $permohonan->groupBy(function ($permohonan) {
            return $permohonan->jenisIzin->nama;
        })->map(function ($permohonan) {
            return [
                'usulan_baru' => $permohonan->where('status', 'permohonan_baru')->count(),
                'verifikasi_ulang' => $permohonan->where('status', 'verifikasi_ulang')->count(),
                'verifikasi' => $permohonan->where('status', 'verifikasi')->count(),
                'revisi' => $permohonan->where('status', 'revisi')->count(),
                'selesai' => $permohonan->where('status', 'selesai')->count(),
            ];
        });

        // Ensure all jenis_izin are included
        $permohonanByIzin = $jenisIzin->mapWithKeys(function ($jenis) use ($permohonanByIzin) {
            return [
                $jenis->nama => $permohonanByIzin->get($jenis->nama, [
                    'usulan_baru' => 0,
                    'verifikasi_ulang' => 0,
                    'verifikasi' => 0,
                    'revisi' => 0,
                    'selesai' => 0,
                ])
            ];
        });

        return response()->json([
            'data' => [
                'permohonan' => [
                    'usulan_baru' => $permohonan->where('status', 'permohonan_baru')->count(),
                    'verifikasi_ulang' => $permohonan->where('status', 'verifikasi_ulang')->count(),
                    'verifikasi' => $permohonan->where('status', 'verifikasi')->count(),
                    'revisi' => $permohonan->where('status', 'revisi')->count(),
                    'selesai' => $permohonan->where('status', 'selesai')->count(),
                ],
                'permohonan_by_izin' => $permohonanByIzin,
            ],
        ]);
    }
}
