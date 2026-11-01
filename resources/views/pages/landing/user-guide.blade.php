@extends('layouts.landing.main-base')

@section('content')
<main class="siap-aum-landing fx-landing tb-page" data-testid="user-guide-page">
    <section class="tb-page-hero">
        <div class="lx-hero__bg" aria-hidden="true"><span class="lx-hero__glow"></span><span class="lx-hero__grid"></span><span class="lx-hero__orb lx-hero__orb--a"></span></div>
        <div class="tb-page-hero__copy">
            <a class="tb-back" href="{{ route('home') }}" data-testid="guide-back-link"><i class="isax isax-arrow-left-2"></i> Kembali ke beranda</a>
            <span class="sa-eyebrow"><i></i>Panduan pengguna</span>
            <h1 data-split>Pengajuan izin dalam <em>enam langkah</em> mudah</h1>
            <p>Ikuti alur berikut untuk membuat akun, mengirim permohonan, dan memantau proses izin melalui SIAP AUM.</p>
            <div class="lx-hero__actions tb-page-hero__actions">
                <a class="sa-btn sa-btn--primary" data-magnetic href="{{ route('register.index') }}" data-testid="guide-register-btn">Mulai Daftar <i class="isax isax-arrow-right-3"></i></a>
                <a class="sa-btn sa-btn--light" data-magnetic href="{{ route('login.index') }}" data-testid="guide-login-btn">Masuk ke Akun</a>
            </div>
        </div>
        <div class="tb-page-hero__art tb-guide-orbit" aria-hidden="true">
            <span class="tb-guide-orbit__ring"></span><span class="tb-guide-orbit__ring tb-guide-orbit__ring--2"></span>
            <span class="tb-guide-orbit__core"><b>6</b><small>Langkah</small></span>
            @foreach (['user-add', 'login', 'add-square', 'document-text', 'clipboard-text', 'tick-circle'] as $i => $icon)
                <i class="isax isax-{{ $icon }}" style="--a: {{ $i * 60 }}deg"></i>
            @endforeach
        </div>
    </section>

    @php
        $guides = [
            ['Daftar akun', 'Daftarkan akun menggunakan email aktif untuk mengakses layanan permohonan.', 'user-guide-1.svg'],
            ['Masuk ke SIAP AUM', 'Masuk menggunakan akun yang telah terdaftar untuk memulai pengajuan.', 'user-guide-2.svg'],
            ['Buat pengajuan', 'Pilih tombol pengajuan baru dari dashboard pemohon.', 'user-guide-3.svg'],
            ['Pilih jenis izin', 'Tentukan jenis perizinan yang sesuai dengan kebutuhan Anda.', 'user-guide-4.svg'],
            ['Lengkapi data', 'Isi formulir dan unggah seluruh dokumen persyaratan dengan teliti.', 'user-guide-5.svg'],
            ['Pantau hingga selesai', 'Pantau status, lakukan revisi bila diminta, lalu unduh izin yang telah terbit.', 'user-guide-6.svg'],
        ];
    @endphp
    <section class="tb-guide-grid" data-testid="guide-steps">
        @foreach ($guides as $index => $guide)
            <article class="tb-guide-card" data-reveal data-spot data-tilt data-testid="guide-step-{{ $index + 1 }}">
                <div class="tb-guide-card__top"><b>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</b><span>Langkah</span></div>
                <img src="{{ asset('assets/images/' . $guide[2]) }}" alt="" aria-hidden="true" loading="lazy" decoding="async">
                <h2>{{ $guide[0] }}</h2>
                <p>{{ $guide[1] }}</p>
            </article>
        @endforeach
    </section>
</main>
@endsection
