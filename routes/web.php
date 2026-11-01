<?php

use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\GroupLayananSkm;
use App\Services\LaporanService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisIzinController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\LayananSkmController;
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\SektorIzinController;
use App\Http\Controllers\UserReklameController;
use App\Http\Controllers\KategoriIzinController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\LogSistemController;
use App\Http\Controllers\UserPermohonanController;
use App\Http\Controllers\VerifikatorPermohonanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LandingController::class, 'index'])->name('home');


Route::get('/panduan-pengguna', [LandingController::class, 'userGuideIndex'])->name('user-guide');;

Route::prefix('cek-permohonan')->name('cek-permohonan.')->group(function () {
    Route::get('/', [LandingController::class, 'cekPermohonanIndex'])->name('index');
    Route::post('/', [LandingController::class, 'cekPermohonanStore'])->name('store');
});

Route::get('/berkas-not-found', [LandingController::class, 'berkasNotFound'])->name('berkas-not-found');

Route::middleware(['guest'])->group(function () {
    // login
    Route::prefix('login')->name('login.')->group(function () {
        Route::get('/', [AuthenticationController::class, 'login'])
            ->name('index');
        Route::post('/', [AuthenticationController::class, 'authenticate'])
            ->name('store');
    });

    // forgot password
    Route::prefix('forgot-password')->name('forgot-password.')->group(function () {
        Route::get('/', [AuthenticationController::class, 'forgotPassword'])
            ->name('index');
        Route::post('/', [AuthenticationController::class, 'sendResetLinkEmail'])
            ->name('store');
    });

    // reset password
    Route::prefix('reset-password')->name('password.')->group(function () {
        Route::get('/{token}', [AuthenticationController::class, 'resetPassword'])
            ->name('reset');
        Route::post('/{token}', [AuthenticationController::class, 'updatePasswordReset'])
            ->name('update');
    });

    // register
    Route::prefix('register')->name('register.')->group(function () {
        Route::get('/', [AuthenticationController::class, 'register'])
            ->name('index');
        Route::post('/', [AuthenticationController::class, 'store'])
            ->name('store');

        // otp
        Route::prefix('otp')->name('otp.')->group(function () {
            Route::post('/generate', [AuthenticationController::class, 'generateOtp'])
                ->name('generate');
        });
    });
});


// auth
Route::middleware(['auth'])->group(function () {
    // My Profile
    Route::get('profile', [AuthenticationController::class, 'profile'])->name('profile.index');
    Route::put('profile', [AuthenticationController::class, 'updateProfile'])->name('profile.update');
    Route::put('update-password', [AuthenticationController::class, 'updatePassword'])->name('update-password');

    Route::middleware(['is_filled_data_register'])->group(function () {
        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Admin Deploy
        Route::middleware(['role:admin'])->group(function () {
            Route::post('admin/deploy', [DashboardController::class, 'deploy'])->name('admin.deploy');
        });
        
        // All Role
        Route::get('download-izin-terbit/{permohonan}/{filepath}', [PermohonanController::class, 'downloadIzinTerbit'])->name('download-izin-terbit');

        // PUBLIC
        Route::middleware(['role:public'])->group(function () {
            Route::prefix('public')->name('public.')->group(function () {
                // Permohonan
                Route::prefix('permohonan')->name('permohonan.')->group(function () {
                    // Table
                    Route::get('/permohonan-table', [UserPermohonanController::class, 'permohonanTable'])->name('permohonan-table');
                    Route::get('/jenis-izin-table', [UserPermohonanController::class, 'jenisIzinTable'])->name('jenis-izin-table');

                    // Resource
                    Route::get('/create', [UserPermohonanController::class, 'create'])->name('create');
                    Route::get('/{permohonan}', [UserPermohonanController::class, 'show'])->name('show');
                    Route::get('/create/{jenis_izin}', [UserPermohonanController::class, 'createPermohonan'])->name('create-permohonan');
                    Route::post('/submit-form/{jenis_izin}', [UserPermohonanController::class, 'submitForm'])->name('submit-form');
                    Route::post('/submit-berkas/{permohonan}', [UserPermohonanController::class, 'submitBerkas'])->name('submit-berkas');
                    Route::post('/revisi/{permohonan}', [UserPermohonanController::class, 'revisi'])->name('revisi');
                    Route::delete('/delete/{permohonan}', [UserPermohonanController::class, 'destroy'])->name('destroy');
                    Route::post('/submit-revisi-form/{permohonan}', [UserPermohonanController::class, 'submitRevisiForm'])->name('submit-revisi-form');

                    // Ajax
                    Route::post('/store-berkas/{permohonan}', [UserPermohonanController::class, 'storeBerkas'])->name('store-berkas');
                    Route::post('/upload-bukti-bayar-reklame/{permohonan}', [UserPermohonanController::class, 'uploadBuktiBayarReklame'])->name('upload-bukti-bayar-reklame');
                });

                // Reklame
                Route::prefix('reklame')->name('reklame.')->group(function () {
                    Route::get('/', [UserReklameController::class, 'index'])->name('index');
                    Route::get('/create', [UserReklameController::class, 'create'])->name('create');
                    Route::get('/search', [UserReklameController::class, 'search'])->name('search');
                    Route::get('/registrasi', [UserReklameController::class, 'registrasi'])->name('registrasi');
                    Route::post('/registrasi', [UserReklameController::class, 'storeRegistrasi'])->name('store-registrasi');
                    Route::post('/{nomor_registrasi}', [UserReklameController::class, 'store'])->name('store');
                    Route::delete('/delete/{nomor_registrasi}', [UserReklameController::class, 'destroy'])->name('destroy');
                    Route::get('/create/{nomor_registrasi}', [UserReklameController::class, 'createReklame'])->name('create-reklame');
                    Route::post('/create/{nomor_registrasi}', [UserReklameController::class, 'storeReklame'])->name('store-reklame');
                    Route::get('/edit/{nomor_registrasi}/{reklame}', [UserReklameController::class, 'editReklame'])->name('edit-reklame');
                    Route::put('/edit/{nomor_registrasi}/{reklame}', [UserReklameController::class, 'updateReklame'])->name('update-reklame');
                    Route::delete('/delete/{nomor_registrasi}/{reklame}', [UserReklameController::class, 'destroyReklame'])->name('destroy-reklame');
                });

                // Kuesioner
                Route::prefix('kuesioner')->name('kuesioner.')->group(function () {
                    // Resource
                    Route::get('/create/{permohonan}', [KuesionerController::class, 'create'])->name('create');
                    Route::post('/store/{permohonan}', [KuesionerController::class, 'store'])->name('store');
                });
            });
        });

        // VERIFIKATOR
        Route::middleware(['role:verifikator'])->group(function () {
            Route::prefix('verifikator')->name('verifikator.')->group(function () {
                // Permohonan
                Route::prefix('permohonan')->name('permohonan.')->group(function () {
                    // Table
                    Route::get('/table', [VerifikatorPermohonanController::class, 'permohonanTable'])->name('table');

                    // Resource
                    Route::get('/', [VerifikatorPermohonanController::class, 'index'])->name('index');
                });

                // Verifikasi
                Route::prefix('verifikasi')->name('verifikasi.')->group(function () {
                    // Table
                    Route::get('/verifikasi-table', [VerifikatorPermohonanController::class, 'verifikasiTable'])->name('table');

                    // Resource
                    Route::get('/', [VerifikatorPermohonanController::class, 'verifikasiIndex'])->name('index');
                    Route::get('/show/{permohonan}', [VerifikatorPermohonanController::class, 'show'])->name('show');
                    Route::post('/simpan-verifikasi/{permohonan}', [VerifikatorPermohonanController::class, 'simpanVerifikasi'])->name('verifikasi');
                    Route::post('/update-draft-sk/{permohonan}', [VerifikatorPermohonanController::class, 'updateDraftSk'])->name('update-draft-sk');

                    // Ajax
                    Route::post('/valid-form', [VerifikatorPermohonanController::class, 'validForm'])->name('valid-form');
                    Route::post('/revisi-form', [VerifikatorPermohonanController::class, 'revisiForm'])->name('revisi-form');
                    Route::post('/valid-berkas', [VerifikatorPermohonanController::class, 'validBerkas'])->name('valid-berkas');
                    Route::post('/revisi-berkas', [VerifikatorPermohonanController::class, 'revisiBerkas'])->name('revisi-berkas');
                    Route::post('/upload-surat-permohonan-rekomendasi/{permohonan}', [VerifikatorPermohonanController::class, 'uploadSuratPermohonanRekomendasi'])->name('upload-surat-permohonan-rekomendasi');
                    Route::post('/upload-surat-rekomendasi/{permohonan}', [VerifikatorPermohonanController::class, 'uploadSuratRekomendasi'])->name('upload-surat-rekomendasi');
                    Route::post('/upload-skpd/{permohonan}', [VerifikatorPermohonanController::class, 'uploadSkpd'])->name('upload-skpd');
                    Route::post('/upload-bukti-bayar-reklame/{permohonan}', [VerifikatorPermohonanController::class, 'uploadBuktiBayarReklame'])->name('upload-bukti-bayar-reklame');
                    Route::post('/upload-lampiran-sk/{permohonan}', [VerifikatorPermohonanController::class, 'uploadLampiranSk'])->name('upload-lampiran-sk');
                    ROute::post('/generate-ulang-izin-terbit/{permohonan}', [VerifikatorPermohonanController::class, 'generateUlangIzinTerbit'])->name('generate-ulang-izin-terbit');
                });

                Route::prefix('laporan')->name('laporan.')->group(function () {
                    // Rekap Permohonan
                    Route::prefix('rekap-permohonan')->name('rekap-permohonan.')->group(function () {
                        // Resource
                        Route::get('/', [LaporanController::class, 'rekapPermohonanVerifikatorIndex'])->name('index');
                        Route::post('/export', [LaporanController::class, 'rekapPermohonanExport'])->name('export');
                    });
                });
            });
        });

        // ADMINISTRATOR
        Route::middleware(['role:administrator'])->group(function () {});
        Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
            // Permohonan
            Route::prefix('permohonan')->name('permohonan.')->group(function () {
                // Table
                Route::get('/table', [PermohonanController::class, 'permohonanTable'])->name('table');

                // Resource
                Route::get('/', [PermohonanController::class, 'index'])->name('index');
                Route::get('/create', [PermohonanController::class, 'create'])->name('create');
                Route::get('/{id}', [PermohonanController::class, 'show'])->name('show');
                Route::get('/edit/{id}', [PermohonanController::class, 'edit'])->name('edit');
                Route::put('/{id}', [PermohonanController::class, 'update'])->name('update');
                Route::delete('/{id}', [PermohonanController::class, 'destroy'])->name('destroy');

                Route::post('/store-berkas/{permohonan}', [PermohonanController::class, 'storeBerkas'])->name('store-berkas');
                Route::post('/generate-ulang-izin-terbit/{permohonan}', [PermohonanController::class, 'generateUlangIzinTerbit'])->name('generate-ulang-izin-terbit');
                // store berkas bukti bayar reklame
                Route::post('/upload-bukti-bayar-reklame/{permohonan}', [PermohonanController::class, 'uploadBuktiBayarReklame'])->name('upload-bukti-bayar-reklame');
                Route::post('/upload-skpd/{permohonan}', [PermohonanController::class, 'uploadSkpd'])->name('upload-skpd');
            });

            // Master Data
            Route::prefix('master-data')->name('master-data.')->group(function () {
                // User
                Route::prefix('user')->name('user.')->group(function () {
                    // Table
                    Route::get('/table', [UserController::class, 'userTable'])->name('table');

                    // Resource
                    Route::get('/', [UserController::class, 'index'])->name('index');
                    Route::get('/create', [UserController::class, 'create'])->name('create');
                    Route::post('/', [UserController::class, 'store'])->name('store');
                    Route::get('/{id}',  [UserController::class, 'show'])->name('show');
                    Route::put('/{id}', [UserController::class, 'update'])->name('update');
                });

                // Jenis Izin
                Route::prefix('jenis-izin')->name('jenis-izin.')->group(function () {
                    // Table
                    Route::get('/table', [JenisIzinController::class, 'jenisIzinTable'])->name('table');

                    // Ajax
                    Route::get('/list-verifikator', [UserController::class, 'getListVerifikator'])->name('list-verifikator');

                    // Resource
                    Route::get('/', [JenisIzinController::class, 'index'])->name('index');
                    Route::get('/create', [JenisIzinController::class, 'create'])->name('create');
                    Route::post('/', [JenisIzinController::class, 'store'])->name('store');
                    Route::get('/{id}', [JenisIzinController::class, 'show'])->name('show');
                    Route::put('/{id}', [JenisIzinController::class, 'update'])->name('update');
                    // Route::delete('/{id}', [JenisIzinController::class, 'destroy'])->name('destroy');
                });

                // Sektor Izin
                Route::prefix('sektor-izin')->name('sektor-izin.')->group(function () {
                    // Table
                    Route::get('/table', [SektorIzinController::class, 'sektorIzinTable'])->name('table');

                    // Resource
                    Route::get('/', [SektorIzinController::class, 'index'])->name('index');
                    Route::get('/create', [SektorIzinController::class, 'create'])->name('create');
                    Route::post('/', [SektorIzinController::class, 'store'])->name('store');
                    Route::get('/{id}', [SektorIzinController::class, 'show'])->name('show');
                    Route::put('/{id}', [SektorIzinController::class, 'update'])->name('update');
                    Route::delete('/{id}', [SektorIzinController::class, 'destroy'])->name('destroy');
                });

                // Kateogri Izin
                Route::prefix('kategori-izin')->name('kategori-izin.')->group(function () {
                    // Table
                    Route::get('/table', [KategoriIzinController::class, 'kategoriIzinTable'])->name('table');

                    // Resource
                    Route::get('/', [KategoriIzinController::class, 'index'])->name('index');
                    Route::get('/create', [KategoriIzinController::class, 'create'])->name('create');
                    Route::post('/', [KategoriIzinController::class, 'store'])->name('store');
                    Route::get('/{id}', [KategoriIzinController::class, 'show'])->name('show');
                    Route::put('/{id}', [KategoriIzinController::class, 'update'])->name('update');
                    Route::delete('/{id}', [KategoriIzinController::class, 'destroy'])->name('destroy');
                });

                // Layanan SKM
                Route::prefix('layanan-skm')->name('layanan-skm.')->group(function () {
                    // Table
                    Route::get('/table', [LayananSkmController::class, 'layananSkmTable'])->name('table');
                    Route::get('/group-skm-table', [LayananSkmController::class, 'groupSkmTable'])->name('group-skm-table');

                    // Resource
                    Route::get('/', [LayananSkmController::class, 'index'])->name('index');
                    Route::get('/create', [LayananSkmController::class, 'create'])->name('create');
                    Route::post('/', [LayananSkmController::class, 'store'])->name('store');
                    Route::get('/{id}', [LayananSkmController::class, 'show'])->name('show');
                    Route::put('/{id}', [LayananSkmController::class, 'update'])->name('update');
                    Route::delete('/{id}', [LayananSkmController::class, 'destroy'])->name('destroy');

                    // group layanan skm
                    Route::get('/group-skm/create', [LayananSkmController::class, 'createGroupSkm'])->name('group-skm.create');
                    Route::post('/group-skm', [LayananSkmController::class, 'storeGroupSkm'])->name('group-skm.store');
                    Route::get('/group-skm/{id}', [LayananSkmController::class, 'showGroupSkm'])->name('group-skm.show');
                    Route::put('/group-skm/{id}', [LayananSkmController::class, 'updateGroupSkm'])->name('group-skm.update');
                });

                // Survey
                Route::prefix('survey')->name('survey.')->group(function () {
                    // Table
                    Route::get('/table', [SurveyController::class, 'surveyTable'])->name('table');

                    // Resource
                    Route::get('/', [SurveyController::class, 'index'])->name('index');
                    Route::get('/create', [SurveyController::class, 'create'])->name('create');
                    Route::get('/{id}', [SurveyController::class, 'show'])->name('show');
                    Route::post('/', [SurveyController::class, 'store'])->name('store');
                    Route::put('/{id}', [SurveyController::class, 'update'])->name('update');
                });
            });

            // Laporan
            Route::prefix('laporan')->name('laporan.')->group(function () {
                // Ijin terbit bulanan
                Route::prefix('ijin-terbit-bulanan')->name('ijin-terbit-bulanan.')->group(function () { // Resource
                    Route::get('/', [LaporanController::class, 'ijinTerbitBulananIndex'])->name('index');
                    Route::get('/export', [LaporanController::class, 'ijinTerbitBulananExport'])->name('export');
                });

                // Rekap Permohonan
                Route::prefix('rekap-permohonan')->name('rekap-permohonan.')->group(function () {
                    // Resource
                    Route::get('/', [LaporanController::class, 'rekapPermohonanIndex'])->name('index');
                    Route::post('/export', [LaporanController::class, 'rekapPermohonanExport'])->name('export');
                });

                // Survey Layanan
                Route::prefix('survey-layanan')->name('survey-layanan.')->group(function () {
                    // Resource
                    Route::get('/', [LaporanController::class, 'surveyLayananIndex'])->name('index');
                    Route::post('/export', [LaporanController::class, 'surveyLayananExport'])->name('export');
                });

                // Buku Tamu
                Route::prefix('buku-tamu')->name('buku-tamu.')->group(function () {
                    // Table
                    Route::get('/table', [LaporanController::class, 'bukuTamuTable'])->name('table');

                    // Resource
                    Route::get('/', [LaporanController::class, 'bukuTamuIndex'])->name('index');
                    Route::post('/export', [LaporanController::class, 'bukuTamuExport'])->name('export');
                });
            });

            // Log Sistem
            Route::prefix('log-sistem')->name('log-sistem.')->group(function () {
                // Otp Gagal
                Route::prefix('otp-gagal')->name('otp-gagal.')->group(function () {
                    // Resource
                    Route::get('/', [LogSistemController::class, 'otpGagalIndex'])->name('index');
                });
                // Otp Sukses
                Route::prefix('otp-sukses')->name('otp-sukses.')->group(function () {
                    // Resource
                    Route::get('/', [LogSistemController::class, 'otpSuksesIndex'])->name('index');
                });
            });
        });
    });

    // Profile
    // Route::prefix('profile')->name('profile.')->group(function () {
    //     Route::get('/', [ProfileController::class, 'index'])
    //         ->name('index');
    //     Route::post('/', [ProfileController::class, 'store'])
    //         ->name('store');
    // });

    // Logout
    Route::post('logout', [AuthenticationController::class, 'logout'])
        ->name('logout');
});
