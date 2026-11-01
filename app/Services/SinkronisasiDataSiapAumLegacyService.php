<?php

namespace App\Services;

use App\Enums\JenisVerifikatorEnum;
use App\Models\Role;
use App\Models\User;
use App\Models\JenisIzin;
use App\Models\Kuesioner;
use App\Models\Permohonan;
use App\Models\AlurJenisIzin;
use App\Models\FormJenisIzin;
use App\Models\KuesionerOpsi;
use App\Models\AlurPermohonan;
use App\Models\FormPermohonan;
use App\Models\ValidasiBerkas;
use App\Models\BerkasJenisIzin;
use App\Models\BerkasPermohonan;
use App\Models\KuesionerJawaban;
use App\Models\PembayaranReklame;
use App\Models\RegistrasiReklame;
use Illuminate\Support\Facades\DB;
use App\Models\KuesionerPertanyaan;
use Illuminate\Support\Facades\Log;
use App\Models\KelengkapanJenisIzin;
use App\Models\KelengkapanPermohonan;
use App\Models\Reklame;
use App\Models\ValidasiForm;

class SinkronisasiDataSiapAumLegacyService
{
    public function __construct()
    {
        // // USERS
        // $start = microtime(true);
        // Log::info('Start Sinkronisasi Data SIAP AUM Legacy Pada : ' . date('Y-m-d H:i:s'));
        // Log::info('==================================================================');
        // Log::info('Start Sinkronisasi Data Users');
        // $start = microtime(true);
        // $this->sinkronRoles();
        // Log::info('Finish Sinkronisasi Data Users : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Jenis Izin');
        // $start = microtime(true);
        // $this->sinkronUsers();
        // Log::info('Finish Sinkronisasi Data Users : ' . (microtime(true) - $start) . 's');
        // Log::info('================');

        // // JENIS IZIN
        // Log::info('Start Sinkronisasi Data Jenis Izin');
        // $start = microtime(true);
        // $this->sinkronJenizIzin();
        // Log::info('Finish Sinkronisas Data Jenis Izin : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Form Jenis Izin');
        // $start = microtime(true);
        // $this->sinkronFormJenisIzin();
        // Log::info('Finish Sinkronisas Data Form Jenis Izin : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Berkas Jenis Izin');
        // $start = microtime(true);
        // $this->sinkronBerkasJenisIzin();
        // Log::info('Finish Sinkronisas Data Berkas Jenis Izin : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Alur Jenis Izin');
        // $start = microtime(true);
        // $this->sinkronKelengkapanJenisIzin();
        // Log::info('Finish Sinkronisas Data Alur Jenis Izin : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Kelengkapan Jenis Izin');
        // $start = microtime(true);
        // $this->sinkronAlurJenisIzin();
        // Log::info('Finish Sinkronisas Data Kelengkapan Jenis Izin : ' . (microtime(true) - $start) . 's');
        // Log::info('================');

        // // PERMOHONAN
        // Log::info('Start Sinkronisasi Data Permohonan');
        // $start = microtime(true);
        // $this->sinkronPermohonan();
        // Log::info('Finish Sinkronisas Data Permohonan : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Form Permohonan');
        // $start = microtime(true);
        // $this->sinkronFormPermohonan();
        // Log::info('Finish Sinkronisas Data Form Permohonan : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Berkas Permohonan');
        // $start = microtime(true);
        // $this->sinkronBerkasPermohonan();
        // Log::info('Finish Sinkronisas Data Berkas Permohonan : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Alur Permohonan');
        // $start = microtime(true);
        // $this->sinkronAlurPermohonan();
        // Log::info('Finish Sinkronisas Data Alur Permohonan : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Validasi Berkas Permohonan');
        // $start = microtime(true);
        // $this->sinkronValidasiBerkasPermohonan();
        // Log::info('Finish Sinkronisas Validasi Berkas Permohonan : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Kelengkapan Permohonan');
        // $start = microtime(true);
        // $this->sinkronKelengkapanPermohonan();
        // Log::info('Finish Sinkronisas Data Kelengkapan Permohonan : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // $this->sinkronValidasiFormPermohonan();
        // Log::info('Finish Sinkronisas Data Validasi Form Permohonan : ' . (microtime(true) - $start) . 's');
        // Log::info('================');

        // // Kuesioner
        // Log::info('Start Sinkronisasi Data Kuesioner');
        // $start = microtime(true);
        // $this->sinkronKuesioner();
        // Log::info('Finish Sinkronisas Data Kuesioner : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Kuesioner Pertanyaan');
        // $start = microtime(true);
        // $this->sinkronKuesionerPertanyaan();
        // Log::info('Finish Sinkronisas Data Kuesioner Pertanyaan : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Kuesioner Opsi');
        // $start = microtime(true);
        // $this->sinkronKuesionerOpsi();
        // Log::info('Finish Sinkronisas Data Kuesioner Opsi : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Kuesioner Jawaban');
        // $start = microtime(true);
        // $this->sinkronKuesionerJawaban();
        // Log::info('Finish Sinkronisas Data Kuesioner Jawaban : ' . (microtime(true) - $start) . 's');
        // Log::info('================');

        // // Reklame
        // Log::info('Start Sinkronisasi Data Reklame');
        // $start = microtime(true);
        // $this->sinkronRegistrasiReklame();
        // Log::info('Finish Sinkronisas Data Reklame : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Pembayaran Reklame');
        // $start = microtime(true);
        // $this->sinkronPembayaranReklame();
        // Log::info('Finish Sinkronisas Data Pembayaran Reklame : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        // Log::info('Start Sinkronisasi Data Reklame');
        // $start = microtime(true);
        // $this->sinkronReklame();
        // Log::info('Finish Sinkronisas Data Reklame : ' . (microtime(true) - $start) . 's');
        // Log::info('================');
        Log::info('Start Sinkronisasi Data Registrasi Reklame to Form Permohonan');
        $start = microtime(true);
        $this->sinkronRegistrasiReklameToFormPermohonan();
        Log::info('Finish Sinkronisas Data Registrasi Reklame to Form Permohonan : ' . (microtime(true) - $start) . 's');
        Log::info('================');


        Log::info('Finish Sinkronisasi Data SIAP AUM Legacy Pada : ' . date('Y-m-d H:i:s'));
        Log::info('==================================================================');
    }

    private function sinkronRoles()
    {
        $rolesLegacy = DB::connection('siapaum_legacy')->table('roles')->get();

        foreach ($rolesLegacy as $role) {
            $role = [
                'id' => $role->id,
                'nama' => $role->nama_role,
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
            ];

            // insert or update data with id
            Role::upsert($role, ['id']);
        }
    }
    private function sinkronUsers()
    {
        $usersLegacy = DB::connection('siapaum_legacy')->table('users')->get();

        foreach ($usersLegacy as $user) {
            $user = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'telepon' => $user->telepon,
                'nik' => $user->nik,
                'jenis_kelamin' => $user->jenis_kelamin,
                'alamat' => $user->alamat,
                'role_id' => $user->id_role,
                'email_verified_at' => $user->email_verified_at,
                'is_filled_data_register' => true,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];

            // insert or update data with id
            User::upsert($user, ['id']);
        }
    }

    private function sinkronJenizIzin()
    {
        $jenisIzinLegacy = DB::connection('siapaum_legacy')->table('jenis_izin')->get();

        foreach ($jenisIzinLegacy as $jenisIzin) {
            // remove base url from template_surat
            if ($jenisIzin->template_surat) {
                $jenisIzin->template_surat = str_replace('https://api-siajaib.bulelengkab.go.id/storage/app/', '', $jenisIzin->template_surat);
            }
            $jenisIzin = [
                'id' => $jenisIzin->id,
                'nama' => $jenisIzin->nama_jenis,
                'deskripsi' => $jenisIzin->deskripsi,
                'template_surat' => $jenisIzin->template_surat,
                'created_at' => $jenisIzin->created_at,
                'updated_at' => $jenisIzin->updated_at,
            ];

            // insert or update data with id
            JenisIzin::upsert($jenisIzin, ['id']);
        }
    }

    private function sinkronFormJenisIzin()
    {
        $formJenisIzinLegacy = DB::connection('siapaum_legacy')->table('detail_form')->get();

        foreach ($formJenisIzinLegacy as $formJenisIzin) {
            // generate urutan based on id, different id, then reset
            $urutan[$formJenisIzin->id_jenis_izin] = $urutan[$formJenisIzin->id_jenis_izin] ?? 0;
            $urutan[$formJenisIzin->id_jenis_izin]++;
            $formJenisIzin = [
                'id' => $formJenisIzin->id,
                'jenis_izin_id' => $formJenisIzin->id_jenis_izin,
                'label' => $formJenisIzin->label,
                'tipe' => $formJenisIzin->id_form_type == 1 ? 'text' : 'date',
                'kode_isian' => $formJenisIzin->kode_isian,
                'urutan' => $urutan[$formJenisIzin->id_jenis_izin],
                'created_at' => $formJenisIzin->created_at,
                'updated_at' => $formJenisIzin->updated_at,
            ];

            // insert or update data with id
            FormJenisIzin::upsert($formJenisIzin, ['id']);
        }
    }

    private function sinkronBerkasJenisIzin()
    {
        $berkasJenisIzinLegacy = DB::connection('siapaum_legacy')->table('berkas_jenis_izin')->get();

        foreach ($berkasJenisIzinLegacy as $berkasJenisIzin) {
            $urutan[$berkasJenisIzin->id_jenis_izin] = $urutan[$berkasJenisIzin->id_jenis_izin] ?? 0;
            $urutan[$berkasJenisIzin->id_jenis_izin]++;
            $berkasJenisIzin = [
                'id' => $berkasJenisIzin->id,
                'jenis_izin_id' => $berkasJenisIzin->id_jenis_izin,
                'nama' => $berkasJenisIzin->nama_berkas,
                'is_required' => $berkasJenisIzin->required,
                'urutan' => $urutan[$berkasJenisIzin->id_jenis_izin],
                'created_at' => $berkasJenisIzin->created_at,
                'updated_at' => $berkasJenisIzin->updated_at,
            ];

            // insert or update data with id
            BerkasJenisIzin::upsert($berkasJenisIzin, ['id']);
        }
    }

    private function sinkronAlurJenisIzin()
    {
        $alurJenisIzinLegacy = DB::connection('siapaum_legacy')->table('alur')->get();

        foreach ($alurJenisIzinLegacy as $alurJenisIzin) {
            $alurJenisIzin = [
                'id' => $alurJenisIzin->id,
                'jenis_izin_id' => $alurJenisIzin->id_jenis_izin,
                'verifikator_id' => $alurJenisIzin->id_user,
                'jenis_verifikator' => $alurJenisIzin->upload,
                'urutan' => $alurJenisIzin->no_urut,
                'created_at' => $alurJenisIzin->created_at,
                'updated_at' => $alurJenisIzin->updated_at,
            ];

            // insert or update data with id
            AlurJenisIzin::upsert($alurJenisIzin, ['id']);
        }
    }

    private function sinkronKelengkapanJenisIzin()
    {
        $kelengkapanJenisIzinLegacy = DB::connection('siapaum_legacy')->table('kelengkapan_izin')->get();

        foreach ($kelengkapanJenisIzinLegacy as $kelengkapanJenisIzin) {
            $urutan[$kelengkapanJenisIzin->id_jenis_izin] = $urutan[$kelengkapanJenisIzin->id_jenis_izin] ?? 0;
            $urutan[$kelengkapanJenisIzin->id_jenis_izin]++;
            $kelengkapanJenisIzin = [
                'id' => $kelengkapanJenisIzin->id,
                'jenis_izin_id' => $kelengkapanJenisIzin->id_jenis_izin,
                'label' => $kelengkapanJenisIzin->label,
                'tipe' => $kelengkapanJenisIzin->id_form_type == 1 ? 'text' : 'date',
                'kode_isian' => $kelengkapanJenisIzin->kode_isian,
                'urutan' => $urutan[$kelengkapanJenisIzin->id_jenis_izin],
                'created_at' => $kelengkapanJenisIzin->created_at,
                'updated_at' => $kelengkapanJenisIzin->updated_at,
            ];

            // insert or update data with id
            KelengkapanJenisIzin::upsert($kelengkapanJenisIzin, ['id']);
        }
    }

    private function sinkronPermohonan()
    {
        $permohonanLegacy = DB::connection('siapaum_legacy')
            ->table('permohonan')
            ->whereNotNull('status')
            ->whereNull('deleted_at')
            ->whereNot('status', 'Tidak Aktif')
            ->get();

        foreach ($permohonanLegacy as $permohonan) {
            if ($permohonan->surat_kuasa) {
                $permohonan->surat_kuasa = str_replace('https://api-siajaib.bulelengkab.go.id/storage/app/', '', $permohonan->surat_kuasa);
            }

            if ($permohonan->surat_permohonan_rekomendasi) {
                $permohonan->surat_permohonan_rekomendasi = str_replace('https://api-siajaib.bulelengkab.go.id/storage/app/', '', $permohonan->surat_permohonan_rekomendasi);
            }

            if ($permohonan->surat_rekomendasi) {
                $permohonan->surat_rekomendasi = str_replace('https://api-siajaib.bulelengkab.go.id/storage/app/', '', $permohonan->surat_rekomendasi);
            }

            if ($permohonan->template_pdf) {
                $permohonan->template_pdf = str_replace('https://api-siajaib.bulelengkab.go.id/storage/app/', '', $permohonan->template_pdf);
            }

            switch ($permohonan->status) {
                case 'Permohonan Baru':
                    $permohonan->status = 'permohonan_baru';
                    break;
                case 'Selesai':
                    $permohonan->status = 'selesai';
                    break;
                case 'Revisi':
                    $permohonan->status = 'revisi';
                    break;
                case 'Verifikasi Ulang':
                    $permohonan->status = 'verifikasi_ulang';
                    break;
                case 'Tidak Aktif':
                    $permohonan->status = 'expired';
                    break;
                case 'Verifikasi':
                    $permohonan->status = 'verifikasi';
                    break;
                default:
                    $permohonan->status = 'pending';
                    break;
            }

            $permohonan = [
                'id' => $permohonan->id,
                'user_id' => $permohonan->id_user,
                'jenis_izin_id' => $permohonan->id_jenis_izin,
                'nama_jenis_izin' => $permohonan->nama_jenis_izin,
                'deskripsi_jenis_izin' => $permohonan->deskripsi_jenis_izin,
                'nomor_registrasi' => $permohonan->no_registrasi,
                'surat_kuasa_filepath' => $permohonan->surat_kuasa,
                'nama' => $permohonan->nama,
                'nik' => $permohonan->nik,
                'npwp' => $permohonan->npwp,
                'tempat_lahir' => $permohonan->tempat_lahir,
                'surat_permohonan_rekomendasi_filepath' => $permohonan->surat_permohonan_rekomendasi,
                'surat_rekomendasi_filepath' => $permohonan->surat_rekomendasi,
                'template_surat_filepath' => $permohonan->template_pdf,
                'is_ttd' => $permohonan->status == 'selesai' ? true : false,
                'status' => $permohonan->status,
                'is_expired' => $permohonan->deleted_at ? true : false,
                'is_legacy_data' => true,
                'pengajuan_at' => $permohonan->created_at,
                'created_at' => $permohonan->created_at,
                'updated_at' => $permohonan->updated_at,
                'deleted_at' => $permohonan->deleted_at,
            ];

            // insert or update data with id
            Permohonan::upsert($permohonan, ['id']);
        }
    }

    private function sinkronFormPermohonan()
    {
        $formPermohonanLegacy = DB::connection('siapaum_legacy')->table('detail_permohonan')
            ->selectRaw('detail_permohonan.*')
            ->join('permohonan', 'detail_permohonan.id_permohonan', '=', 'permohonan.id')
            ->whereNotNull('permohonan.status')
            ->whereNot('permohonan.status', 'Tidak Aktif')
            ->whereNull('permohonan.deleted_at')
            ->get();

        foreach ($formPermohonanLegacy as $formPermohonan) {
            $uruatan[$formPermohonan->id_permohonan] = $uruatan[$formPermohonan->id_permohonan] ?? 0;
            $uruatan[$formPermohonan->id_permohonan]++;
            $formPermohonan = [
                'id' => $formPermohonan->id,
                'permohonan_id' => $formPermohonan->id_permohonan,
                'label' => $formPermohonan->label,
                'tipe' => $formPermohonan->form_type == 1 ? 'text' : 'date',
                'kode_isian' => $formPermohonan->kode_isian,
                'value' => $formPermohonan->value,
                'urutan' => $uruatan[$formPermohonan->id_permohonan],
                'created_at' => $formPermohonan->created_at,
                'updated_at' => $formPermohonan->updated_at,
            ];

            // insert or update data with id
            FormPermohonan::upsert($formPermohonan, ['id']);
        }
    }

    private function sinkronBerkasPermohonan()
    {
        $berkasPermohonanLegacy = DB::connection('siapaum_legacy')
            ->table('berkas_permohonan')
            ->selectRaw('berkas_permohonan.*')
            ->join('permohonan', 'berkas_permohonan.id_permohonan', '=', 'permohonan.id')
            ->whereNotNull('permohonan.status')
            ->where('permohonan.deleted_at', null)
            ->whereNot('permohonan.status', 'Tidak Aktif')
            ->orderBy('berkas_permohonan.id_permohonan')
            ->chunk(1000, function ($berkasPermohonanLegacy) {
                foreach ($berkasPermohonanLegacy as $berkasPermohonan) {
                    $urutan[$berkasPermohonan->id_permohonan] = $urutan[$berkasPermohonan->id_permohonan] ?? 0;
                    $urutan[$berkasPermohonan->id_permohonan]++;
                    if ($berkasPermohonan->file) {
                        $berkasPermohonan->file = str_replace('https://api-siajaib.bulelengkab.go.id/storage/app/', '', $berkasPermohonan->file);
                    }
                    $berkasPermohonans = [
                        'id' => $berkasPermohonan->id,
                        'permohonan_id' => $berkasPermohonan->id_permohonan,
                        'nama' => $berkasPermohonan->nama_berkas,
                        'is_required' => $berkasPermohonan->required,
                        'urutan' => $urutan[$berkasPermohonan->id_permohonan],
                        'filepath' => $berkasPermohonan->file,
                        'is_revisi' => $berkasPermohonan->ket_revisi ? 1 : 0,
                        'catatan' => $berkasPermohonan->ket_revisi,
                        'created_at' => $berkasPermohonan->created_at,
                        'updated_at' => $berkasPermohonan->updated_at,
                    ];

                    // insert or update data with id
                    BerkasPermohonan::upsert($berkasPermohonans, ['id']);
                }
            });
    }

    private function sinkronAlurPermohonan()
    {
        $alurPermohonanLegacy = DB::connection('siapaum_legacy')
            ->table('alur_permohonan')
            ->selectRaw('alur_permohonan.*')
            ->join('permohonan', 'alur_permohonan.id_permohonan', '=', 'permohonan.id')
            ->whereNotNull('permohonan.status')
            ->whereNot('permohonan.status', 'Tidak Aktif')
            ->whereNull('permohonan.deleted_at')
            ->get();

        foreach ($alurPermohonanLegacy as $alurPermohonan) {
            $urutan[$alurPermohonan->id_permohonan] = $urutan[$alurPermohonan->id_permohonan] ?? 0;
            $urutan[$alurPermohonan->id_permohonan]++;
            $alurPermohonan = [
                'id' => $alurPermohonan->id,
                'permohonan_id' => $alurPermohonan->id_permohonan,
                'verifikator_id' => $alurPermohonan->id_user,
                'jenis_verifikator' => $alurPermohonan->upload,
                'urutan' => $urutan[$alurPermohonan->id_permohonan],
                'is_done' => $alurPermohonan->status,
                'created_at' => $alurPermohonan->created_at,
                'updated_at' => $alurPermohonan->updated_at,
            ];

            // insert or update data with id
            AlurPermohonan::upsert($alurPermohonan, ['id']);
        }
    }

    private function sinkronValidasiBerkasPermohonan()
    {
        $validasiBerkasLegacy = DB::connection('siapaum_legacy')
            ->table('validasi_berkas', 'a')
            ->selectRaw('a.*, berkas_permohonan.ket_revisi')
            ->join('berkas_permohonan', 'a.id_berkas', '=', 'berkas_permohonan.id')
            ->join('alur_permohonan', 'a.id_alur', '=', 'alur_permohonan.id')
            ->join('permohonan', 'berkas_permohonan.id_permohonan', '=', 'permohonan.id')
            ->whereNotNull('permohonan.status')
            ->whereNot('permohonan.status', 'Tidak Aktif')
            ->whereNull('permohonan.deleted_at')
            ->where('berkas_permohonan.deleted_at', null)
            ->where(function ($query) {
                $query->where('alur_permohonan.id_user', 'berkas_permohonan.id_user_revisi')
                    ->orWhereNull('berkas_permohonan.id_user_revisi');
            })
            ->get();

        foreach ($validasiBerkasLegacy as $validasiBerkas) {
            $validasiBerkas = [
                'id' => $validasiBerkas->id,
                'alur_permohonan_id' => $validasiBerkas->id_alur,
                'berkas_permohonan_id' => $validasiBerkas->id_berkas,
                'status' => $validasiBerkas->status_valid ? 'valid' : ($validasiBerkas->ket_revisi ? 'revisi' : 'pending'),
                'catatan' => $validasiBerkas->ket_revisi,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // insert or update data with id
            ValidasiBerkas::upsert($validasiBerkas, ['id']);
        }
    }

    private function sinkronKelengkapanPermohonan()
    {
        $kelengkapanPermohonanLegacy = DB::connection('siapaum_legacy')
            ->table('kelengkapan_permohonan')
            ->selectRaw('kelengkapan_permohonan.*')
            ->join('permohonan', 'kelengkapan_permohonan.id_permohonan', '=', 'permohonan.id')
            ->whereNotNull('permohonan.status')
            ->whereNull('permohonan.deleted_at')
            ->get();

        foreach ($kelengkapanPermohonanLegacy as $kelengkapanPermohonan) {
            $urutan[$kelengkapanPermohonan->id_permohonan] = $urutan[$kelengkapanPermohonan->id_permohonan] ?? 0;
            $urutan[$kelengkapanPermohonan->id_permohonan]++;
            $kelengkapanPermohonan = [
                'id' => $kelengkapanPermohonan->id,
                'permohonan_id' => $kelengkapanPermohonan->id_permohonan,
                'label' => $kelengkapanPermohonan->label,
                'tipe' => $kelengkapanPermohonan->id_form_type == 1 ? 'text' : 'date',
                'kode_isian' => $kelengkapanPermohonan->kode_isian,
                'value' => $kelengkapanPermohonan->value,
                'urutan' => $urutan[$kelengkapanPermohonan->id_permohonan],
                'created_at' => $kelengkapanPermohonan->created_at,
                'updated_at' => $kelengkapanPermohonan->updated_at,
            ];

            // insert or update data with id
            KelengkapanPermohonan::upsert($kelengkapanPermohonan, ['id']);
        }
    }

    private function sinkronValidasiFormPermohonan()
    {
        $alurPermohonanExisting = AlurPermohonan::query()
            ->whereIn('jenis_verifikator', [
                JenisVerifikatorEnum::FO->value,
                JenisVerifikatorEnum::OPD->value,
                JenisVerifikatorEnum::BO->value,
            ])
            ->where('is_done', true)
            ->get();


        foreach ($alurPermohonanExisting as $alurPermohonan) {
            $validasiBerkas = [
                'id' => $alurPermohonan->id,
                'alur_permohonan_id' => $alurPermohonan->id,
                'status' => 'valid',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // insert or update data with id
            ValidasiForm::upsert($validasiBerkas, ['id']);
        }
    }

    private function sinkronKuesioner()
    {
        $kuesionerLegacy = DB::connection('siapaum_legacy')
            ->table('kuisioner')
            ->selectRaw('kuisioner.*, permohonan.id_jenis_izin, users.name as nama_user, users.email as email_user, users.telepon as telepon_user')
            ->whereNull('id_skm')
            ->join('permohonan', 'kuisioner.id_permohonan', '=', 'permohonan.id')
            ->whereNotNull('permohonan.status')
            ->whereNot('permohonan.status', 'Tidak Aktif')
            ->whereNull('permohonan.deleted_at')
            ->join('users', 'permohonan.id_user', '=', 'users.id')
            ->get();

        foreach ($kuesionerLegacy as $kuesioner) {
            $kuesioner = [
                'id' => $kuesioner->id,
                'permohonan_id' => $kuesioner->id_permohonan,
                'jenis_kelamin' => $kuesioner->jenis_kelamin == 'L' ? 1 : 0,
                'pendidikan' => $kuesioner->pendidikan,
                'pekerjaan' => $kuesioner->pekerjaan,
                'jenis_layanan' => $kuesioner->jenis_layanan,
                'created_at' => $kuesioner->created_at,
                'updated_at' => $kuesioner->updated_at,
                'jenis_izin_id' => $kuesioner->id_jenis_izin,
                'nama' => $kuesioner->nama_user,
                'email' => $kuesioner->email_user,
                'nomor_telepon' => $kuesioner->telepon_user,
            ];

            // insert or update data with id
            Kuesioner::upsert($kuesioner, ['id']);
        }
    }

    private function sinkronKuesionerPertanyaan()
    {
        $kuesionerPertanyaanLegacy = DB::connection('siapaum_legacy')->table('kuisioner_pertanyaan')->get();

        foreach ($kuesionerPertanyaanLegacy as $kuesionerPertanyaan) {
            $kuesionerPertanyaan = [
                'id' => $kuesionerPertanyaan->id,
                'pertanyaan' => $kuesionerPertanyaan->pertanyaan,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // insert or update data with id
            KuesionerPertanyaan::upsert($kuesionerPertanyaan, ['id']);
        }
    }

    private function sinkronKuesionerOpsi()
    {
        $kuesionerOpsiLegacy = DB::connection('siapaum_legacy')->table('kuisioner_opsi')->get();

        $remove_char = ['a. ', 'b. ', 'c. ', 'd. ', 'e. '];

        foreach ($kuesionerOpsiLegacy as $kuesionerOpsi) {
            $kuesionerOpsi = [
                'id' => $kuesionerOpsi->id,
                'kuesioner_pertanyaan_id' => $kuesionerOpsi->id_pertanyaan,
                'opsi' => str_replace($remove_char, '', $kuesionerOpsi->opsi),
                'point' => $kuesionerOpsi->point,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // insert or update data with id
            KuesionerOpsi::upsert($kuesionerOpsi, ['id']);
        }
    }

    private function sinkronKuesionerJawaban()
    {
        $kuesionerJawabanLegacy = DB::connection('siapaum_legacy')
            ->table('kuisioner_jawaban', 'a')
            ->selectRaw('a.*, kuisioner_pertanyaan.id as id_kuisioner_pertanyaan, kuisioner_pertanyaan.pertanyaan as pertanyaan, kuisioner_opsi.opsi as opsi, kuisioner_opsi.point as point')
            ->join('kuisioner', 'a.id_kuisioner', '=', 'kuisioner.id')
            ->join('kuisioner_opsi', 'a.id_opsi', '=', 'kuisioner_opsi.id')
            ->join('permohonan', 'kuisioner.id_permohonan', '=', 'permohonan.id')
            ->whereNotNull('permohonan.status')
            ->whereNot('permohonan.status', 'Tidak Aktif')
            ->whereNull('permohonan.deleted_at')
            ->join('kuisioner_pertanyaan', 'kuisioner_opsi.id_pertanyaan', '=', 'kuisioner_pertanyaan.id')
            ->whereNull('kuisioner.id_skm')
            ->get();

        $remove_char = ['a. ', 'b. ', 'c. ', 'd. ', 'e. '];

        foreach ($kuesionerJawabanLegacy as $kuesionerJawaban) {
            $kuesionerJawaban = [
                'id' => $kuesionerJawaban->id,
                'kuesioner_id' => $kuesionerJawaban->id_kuisioner,
                'kuesioner_pertanyaan_id' => $kuesionerJawaban->id_kuisioner_pertanyaan,
                'kuesioner_opsi_id' => $kuesionerJawaban->id_opsi,
                'pertanyaan' => $kuesionerJawaban->pertanyaan,
                'opsi' => str_replace($remove_char, '', $kuesionerJawaban->opsi),
                'point' => $kuesionerJawaban->point,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // insert or update data with id
            KuesionerJawaban::upsert($kuesionerJawaban, ['id']);
        }
    }

    private function sinkronRegistrasiReklame()
    {
        $registrasiReklameLegacy = DB::connection('siapaum_legacy')->table('registrasi_reklame')->get();

        foreach ($registrasiReklameLegacy as $registrasiReklame) {
            $registrasiReklame = [
                'id' => $registrasiReklame->id,
                'user_id' => $registrasiReklame->id_user,
                'nomor_registrasi' => $registrasiReklame->no_reg,
                'nama' => $registrasiReklame->nama_reg,
                'nik' => $registrasiReklame->nik_reg,
                'npwp' => $registrasiReklame->npwp_reg,
                'nama_perusahaan' => $registrasiReklame->nama_perusahaan,
                'alamat_perusahaan' => $registrasiReklame->alamat_perusahaan,
                'nomor_telepon' => $registrasiReklame->no_telp,
                'created_at' => $registrasiReklame->created_at,
                'updated_at' => $registrasiReklame->updated_at,
            ];

            // insert or update data with id
            RegistrasiReklame::upsert($registrasiReklame, ['id']);
        }
    }

    private function sinkronPembayaranReklame()
    {
        $pembayaranReklameLegacy = DB::connection('siapaum_legacy')->table('reklame_pembayaran')
            ->select('reklame_pembayaran.*')
            ->join('permohonan', 'reklame_pembayaran.id_permohonan', '=', 'permohonan.id')
            ->whereNotNull('permohonan.status')
            ->whereNot('permohonan.status', 'Tidak Aktif')
            ->whereNull('permohonan.deleted_at')
            ->get();


        foreach ($pembayaranReklameLegacy as $pembayaranReklame) {
            if ($pembayaranReklame->fileskpd) {
                $pembayaranReklame->fileskpd = str_replace('https://api-siajaib.bulelengkab.go.id/storage/app/', '', $pembayaranReklame->fileskpd);
            }

            $pembayaranReklame = [
                'id' => $pembayaranReklame->id,
                'permohonan_id' => $pembayaranReklame->id_permohonan,
                'nomor_skpd' => $pembayaranReklame->no_skpd,
                'is_lunas' => $pembayaranReklame->statuslunas,
                'skpd_filepath' => $pembayaranReklame->fileskpd,
                'nominal' => $pembayaranReklame->nominal,
                'created_at' => $pembayaranReklame->created_at,
                'updated_at' => $pembayaranReklame->updated_at,
            ];

            // insert or update data with id
            PembayaranReklame::upsert($pembayaranReklame, ['id']);
        }
    }

    private function sinkronReklame()
    {
        $reklame_legacy = DB::connection('siapaum_legacy')->table('reklame_pembayaran')
        ->select('reklame_pembayaran.*')
        ->join('permohonan', 'reklame_pembayaran.id_permohonan', '=', 'permohonan.id')
        ->whereNotNull('permohonan.status')
        ->whereNot('permohonan.status', 'Tidak Aktif')
        ->whereNull('permohonan.deleted_at')
        ->get();

        foreach ($reklame_legacy as $reklames) {
            $reklame = [
                'id' => $reklames->id,
                'permohonan_id' => $reklames->id_permohonan,
                // 'nomor_skpd' => $reklame->no_skpd,
                // 'is_lunas' => $reklame->statuslunas,
                'skpd_filepath' => $reklames->fileskpd,
                // 'nominal' => $reklame->nominal,
                'created_at' => $reklames->created_at,
                'updated_at' => $reklames->updated_at,
            ];


            // insert or update data with id
            $reklame = Reklame::firstOrCreate(['id' => $reklame['id']], $reklame);

            // image
            $permohonan = Permohonan::where('id', $reklames->id_permohonan)->first();
            $reklame_image = $permohonan->berkasPermohonan()->where('nama', 'Foto Tempat Pemasangan Reklame')->first()->filepath;
            // registrasi_reklame_id
            $registrasi_reklame_id = RegistrasiReklame::where('nomor_registrasi', $permohonan->nomor_registrasi)->first()->id;

            $reklame->update([
                'registrasi_reklame_id' => $registrasi_reklame_id,
                'image_filepath' => $reklame_image,
            ]);
        }

        $permohonan = Permohonan::with('reklame')->where('jenis_izin_id', 9)->get();
        foreach ($permohonan as $permohonans) {
            $no_skpd = $permohonans->pembayaranReklame->nomor_skpd;
            $permohonans->kelengkapanPermohonan()->create([
                'label' => 'Nomor SKPD',
                'tipe' => 'text',
                'kode_isian' => 'NO_SKPD',
                'value' => $no_skpd,
            ]);
        }

    }

    private function sinkronRegistrasiReklameToFormPermohonan()
    {
        $permohonans = Permohonan::with('formPermohonan')->where('jenis_izin_id', 9)->whereDoesntHave('formPermohonan', function ($query) {    $query->where('kode_isian', 'NAMA_PERUSAHAAN');})->get();

        $registrasi_reklame = RegistrasiReklame::whereIn('nomor_registrasi', $permohonans->pluck('nomor_registrasi'))->get();

        foreach ($permohonans as $permohonan) {
            $registrasi = $registrasi_reklame->where('nomor_registrasi', $permohonan->nomor_registrasi)->first();
            // get last urutan from form permohonan
            $urutan = $permohonan->formPermohonan->max('urutan') ?? 0;
            // delete kelengkapan permohonan
            $permohonan->kelengkapanPermohonan()->where('kode_isian', 'ALAMAT')->delete();
            $permohonan->kelengkapanPermohonan()->where('kode_isian', 'NAMA_PERUSAHAAN')->delete();
            $permohonan->kelengkapanPermohonan()->where('kode_isian', 'HP/TELP')->delete();
            // insert alamat perusahaan, nomor telepon, nama perusahaan
            $permohonan->formPermohonan()->create([
                'label' => 'Alamat',
                'tipe' => 'text',
                'kode_isian' => 'ALAMAT',
                'value' => $registrasi->alamat_perusahaan,
                'urutan' => $urutan + 1,
            ]);
            $permohonan->formPermohonan()->create([
                'label' => 'Nama Perusahaan',
                'tipe' => 'text',
                'kode_isian' => 'NAMA_PERUSAHAAN',
                'value' => $registrasi->nama_perusahaan,
                'urutan' => $urutan + 2,
            ]);
            $permohonan->formPermohonan()->create([
                'label' => 'No. Telp/HP',
                'tipe' => 'text',
                'kode_isian' => 'HP/TELP',
                'value' => $registrasi->nomor_telepon,
                'urutan' => $urutan + 3,
            ]);
        }
    }
}
