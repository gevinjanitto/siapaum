@extends('layouts.landing.main-base')

@section('content')
<main class="siap-aum-landing fx-landing tb-page" data-testid="cek-permohonan-page">
    <section class="tb-page-hero">
        <div class="lx-hero__bg" aria-hidden="true"><span class="lx-hero__glow"></span><span class="lx-hero__grid"></span><span class="lx-hero__orb lx-hero__orb--a"></span></div>
        <div class="tb-page-hero__copy">
            <a class="tb-back" href="{{ route('home') }}" data-testid="cek-back-link"><i class="isax isax-arrow-left-2"></i> Kembali ke beranda</a>
            <span class="sa-eyebrow"><i></i>Lacak permohonan</span>
            <h1 data-split>Cek status <em>izin Anda</em> secara real-time</h1>
            <p>Masukkan nomor registrasi untuk melihat posisi berkas dan riwayat proses permohonan.</p>
        </div>
        <div class="tb-page-hero__art" aria-hidden="true">
            <div class="tb-art tb-art--track tb-hero-track">
                <div class="tb-track__head"><span class="lx-live"><i></i>Live</span><strong>REG/2026/&bull;&bull;&bull;&bull;</strong></div>
                <div class="tb-track__line"><span class="tb-track__fill"></span>
                    <div class="tb-track__node is-done"><i class="isax-bold isax-document-upload"></i><small>Diajukan</small></div>
                    <div class="tb-track__node is-done"><i class="isax-bold isax-search-status"></i><small>Diperiksa</small></div>
                    <div class="tb-track__node is-now"><i class="isax-bold isax-shield-search"></i><small>Verifikasi</small></div>
                    <div class="tb-track__node"><i class="isax-bold isax-document-text"></i><small>Terbit</small></div>
                </div>
            </div>
        </div>
    </section>

    <section class="tb-track-card" data-reveal data-spot>
        <form action="{{ route('cek-permohonan.store') }}" class="fx-check__form tb-track-form" method="POST" data-testid="cek-form">
            @csrf
            <label class="visually-hidden" for="nomor_registrasi">Nomor registrasi</label>
            <i class="isax isax-receipt-search"></i>
            <input id="nomor_registrasi" name="nomor_registrasi" placeholder="Contoh: REG/2026/00001" type="text" value="{{ $nomor_registrasi ?? '' }}" required data-testid="cek-input">
            <button type="submit" data-magnetic data-testid="cek-submit-btn" aria-label="Cari permohonan"><span>Cari Permohonan</span><i class="isax isax-search-normal-1"></i></button>
        </form>
        @if (isset($is_found) && $is_found)
            <div class="sa-track-result" data-testid="cek-result">
                <x-landing.progress-stepper-bar :steps="$steps" />
                <x-log-aktivitas-table :permohonan=$permohonan />
                <div class="sa-track-details">
                    <x-dashboard.input-inline-text class="bg-white p-2 mb-3 text-xsm" class_input="border-0 text-end text-xsm" label="No Registrasi" name="nomor_registrasi" readonly value="{{ $permohonan->nomor_registrasi }}" />
                    <x-dashboard.input-inline-text class="bg-white p-2 mb-3 text-xsm" class_input="border-0 text-end text-xsm" label="Jenis Perizinan" name="jenis_izin" readonly value="{{ $permohonan->jenisIzin->nama }}" />
                    <x-dashboard.input-inline-text class="bg-white p-2 mb-3 text-xsm" class_input="border-0 text-end text-xsm" label="Nama" name="nama" readonly value="{{ $permohonan->nama }}" />
                    <x-dashboard.input-inline-text class="bg-white p-2 mb-3 text-xsm" class_input="border-0 text-end text-xsm" label="Status" name="status" readonly value="{{ $permohonan->status }}" />
                </div>
            </div>
        @elseif(isset($is_found))
            <div class="sa-track-empty" data-testid="cek-not-found"><i class="isax isax-info-circle"></i><p>Permohonan dengan nomor registrasi <strong>{{ $nomor_registrasi }}</strong> tidak ditemukan.</p></div>
        @endif
    </section>

    <section class="tb-tips">
        @foreach ([['receipt-search', 'Siapkan nomor registrasi', 'Nomor tercantum pada bukti pengajuan dan email notifikasi Anda.'], ['clock', 'Pantau tiap tahap', 'Lihat riwayat proses dari pengajuan hingga izin diterbitkan.'], ['edit-2', 'Revisi bila diminta', 'Masuk ke akun untuk memperbaiki berkas sesuai catatan petugas.']] as $i => $tip)
            <article class="tb-tip" data-reveal data-spot data-tilt>
                <span class="tb-tip__icon"><i class="isax isax-{{ $tip[0] }}"></i></span>
                <b>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</b>
                <h2>{{ $tip[1] }}</h2>
                <p>{{ $tip[2] }}</p>
            </article>
        @endforeach
    </section>
</main>
@endsection
