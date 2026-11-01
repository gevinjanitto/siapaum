<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReklameController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\OpenApi\AuthenticationController as OpenApiAuthenticationController;
use App\Http\Controllers\OpenApi\StatistikController;
use App\Http\Controllers\Skm\SkmController;
use App\Http\Controllers\Skm\TamuController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'role:inputer'])->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    // Reklame
    Route::get('/reklame', [ReklameController::class, 'index']);
    Route::get('/reklame/{id}', [ReklameController::class, 'show']);
    Route::post('/reklame', [ReklameController::class, 'initReklame']);
    Route::post('/reklame/{id}', [ReklameController::class, 'storeReklame']);

    // Syarat Reklame
    Route::get('/syarat-reklame/area-pemasangan', [ReklameController::class, 'getSyaratAreaPemasangan']);
    Route::get('/syarat-reklame/jenis-reklame', [ReklameController::class, 'getSyaratJenisReklame']);

    // addon
    Route::get('/permohonan-reklame', [ReklameController::class, 'getPermohonanReklame']);
    Route::get('/permohonan-reklame/koordinat', [ReklameController::class, 'getKoordinat']);
    Route::get('/permohonan-reklame/{id}', [ReklameController::class, 'getPermohonanReklameById']);
    Route::post('/permohonan-reklame/{id}/bongkar', [ReklameController::class, 'storeBongkar']);
});

// OPEN API
Route::prefix('v1')->group(function () {
    Route::post('/login', [OpenApiAuthenticationController::class, 'login']);
    Route::middleware(['auth:sanctum'])->group(function () {
        // Auth
        Route::post('/logout', [OpenApiAuthenticationController::class, 'logout']);

        // Statistik
        Route::get('/statistik-permohonan', [StatistikController::class, 'index']);
    });
});

// SKM
Route::prefix('skm')->middleware(['auth.skm'])->group(function () {
    Route::get('/jenis-layanan', [SkmController::class, 'jenisLayanan']);
    Route::get('/pertanyaan-opsi', [SkmController::class, 'pertanyaanOpsi']);
    Route::get('/jenis-pekerjaan', [SkmController::class, 'jenisPekerjaan']);
    Route::get('/pendidikan', [SkmController::class, 'pendidikan']);
    Route::get('/jenis-kelamin', [SkmController::class, 'jenisKelamin']);
    Route::get('/group-layanan-skm', [SkmController::class, 'groupLayananSkm']);
    Route::post('/survey-layanan', [SkmController::class, 'surveyLayanan']);

    // Tamu
    Route::post('/tamu', [TamuController::class, 'store']);
});
