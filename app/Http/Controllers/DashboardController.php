<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Illuminate\Http\Request;
use App\Enums\StatusPermohonanEnum;
use App\Http\Controllers\DashboardUserController;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->is_admin) {
            $permohonanAktif = Permohonan::whereNotIn('status', [
                StatusPermohonanEnum::PENDING->value,
                StatusPermohonanEnum::EXPIRED->value
            ]);

            $total_permohonan = (clone $permohonanAktif)->count();

            $permohonan_diproses = (clone $permohonanAktif)->whereNotIn('status', [
                StatusPermohonanEnum::SELESAI->value,
            ])->count();

            $permohonan_selesai = Permohonan::where('status', StatusPermohonanEnum::SELESAI->value)
                ->count();

            $persentase_selesai = $total_permohonan > 0
                ? (int) round(($permohonan_selesai / $total_permohonan) * 100)
                : 0;

            $permohonan_terbaru = (clone $permohonanAktif)
                ->latest('created_at')
                ->limit(5)
                ->get();

            return view('pages.admin.dashboard.index', compact(
                'total_permohonan',
                'permohonan_diproses',
                'permohonan_selesai',
                'persentase_selesai',
                'permohonan_terbaru'
            ));
        } else if (auth()->user()->is_public) {
            $dashboardUserController = new DashboardUserController();
            return $dashboardUserController->index($request);
        } else if (auth()->user()->is_verifikator) {
            $total_permohonan = Permohonan::whereNotIn('status', [
                StatusPermohonanEnum::PENDING->value,
                StatusPermohonanEnum::EXPIRED->value
            ])->whereHas('alurPermohonan', function ($q) {
                $q->where('verifikator_id', auth()->user()->id);
            })->count();

            $permohonan_selesai = Permohonan::where('status', StatusPermohonanEnum::SELESAI->value)
                ->whereHas('alurPermohonan', function ($q) {
                    $q->where('verifikator_id', auth()->user()->id);
                })->count();

            return view('pages.verifikator.dashboard.index', compact(
                'total_permohonan',
                'permohonan_selesai'
            ));
        } else {
            auth()->logout();
            return redirect()->route('login.index')->with('error', 'Anda tidak memiliki akses, silahkan login kembali');
        }
    }

    public function deploy(Request $request)
    {
        if (!auth()->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $deployScriptPath = base_path('deploy.sh');
            
            if (!file_exists($deployScriptPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deploy script tidak ditemukan'
                ], 404);
            }

            // Execute deploy script
            $output = [];
            $returnVar = 0;
            exec("bash {$deployScriptPath} 2>&1", $output, $returnVar);

            if ($returnVar === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aplikasi berhasil diperbarui',
                    'output' => implode("\n", $output)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui aplikasi',
                    'output' => implode("\n", $output)
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
