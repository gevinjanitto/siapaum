@extends('layouts.landing.auth-base')

@section('content')
<main class="sa-login-page sa-register-page tb-login">
    <section class="sa-login-visual tb-gate" data-testid="register-gate-visual">
        <a class="sa-login-back" href="{{ route('home') }}" aria-label="Kembali ke beranda" data-testid="register-back-btn"><i class="isax isax-arrow-left-2"></i></a>
        <span class="tb-gate__brand"><img src="{{ asset('assets/images/siap-aum/siap-aum-mark.svg') }}" alt=""> SIAP AUM</span>
        <div class="tb-gate__bg" aria-hidden="true"><span></span><span></span></div>
        <canvas id="loginGate" data-ornament="{{ asset('assets/images/siap-aum/gate-ornament.jpg') }}" aria-label="Gapura 3D Kabupaten Tabanan" data-testid="register-gate-canvas"></canvas>
        <img class="tb-gate__fallback" src="{{ asset('assets/images/siap-aum/figma-raw-4.webp') }}" alt="Gapura Kabupaten Tabanan">
        <div class="tb-gate__hint" data-testid="register-gate-hint"><i class="isax isax-sms"></i> Klik email untuk membuka palang</div>
        <div class="tb-gate__intro">
            <h1>Daftar akun <em>SIAP AUM</em></h1>
            <p>Sistem Informasi Administrasi Perizinan Andal, Unggul, Nyaman, dan Gesit</p>
        </div>
    </section>

    <section class="sa-login-panel sa-register-panel">
        <div class="sa-login-card sa-register-card tb-login-card">
            <div class="sa-login-card__heading">
                <span class="sa-eyebrow"><i></i>Daftar</span>
                <h2>Buat akun Anda</h2>
                <p>Lengkapi data berikut untuk mendapatkan kode verifikasi melalui email.</p>
            </div>

            <input id="route_otp" type="hidden" value="{{ route('register.otp.generate') }}">
            <input id="route_register" type="hidden" value="{{ route('register.store') }}">
            <input id="csrf_token" type="hidden" value="{{ csrf_token() }}">

            <div class="sa-login-field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Masukkan email aktif" autocomplete="email" required>
                @error('email')<span>{{ $message }}</span>@enderror
            </div>
            <div class="sa-login-field">
                <label for="password">Password</label>
                <div><input id="password" name="password" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required><button class="btn-toggle-password isax isax-eye-slash" type="button" aria-label="Tampilkan password"></button></div>
                <small>Gunakan huruf besar, huruf kecil, angka, dan karakter khusus.</small>
            </div>
            <div class="sa-login-field">
                <label for="password_verify">Konfirmasi password</label>
                <div><input id="password_verify" name="password_verify" type="password" placeholder="Masukkan kembali password" autocomplete="new-password" required><button class="btn-toggle-password isax isax-eye-slash" type="button" aria-label="Tampilkan konfirmasi password"></button></div>
            </div>

            @if (config('recaptcha.enabled'))
                <div class="sa-login-captcha" id="otp-recaptcha">{!! htmlFormSnippet() !!}</div>
            @endif

            <button class="sa-login-submit" id="generate_otp" type="button">Daftar dan Kirim OTP</button>
            <p class="sa-login-register">Sudah Punya Akun? <a href="{{ route('login.index') }}">Masuk</a></p>
            <div class="sa-login-partners"><img src="{{ asset('assets/images/maiharta-blue.png') }}" alt="Maiharta"><img src="{{ asset('assets/images/bsre.png') }}" alt="BSrE"></div>
        </div>
    </section>
</main>

<div aria-hidden="true" aria-labelledby="staticBackdropLabel" class="modal fade sa-otp-modal" data-bs-backdrop="static" data-bs-keyboard="false" id="staticBackdrop" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div><span class="sa-eyebrow"><i></i>Keamanan</span><h1 class="modal-title" id="staticBackdropLabel">Verifikasi Email</h1></div>
                <button aria-label="Tutup" class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
                <p>Masukkan kode OTP yang dikirimkan ke email Anda. Jangan membagikan kode tersebut kepada siapa pun.</p>
                <div class="sa-otp-input-wrap"><x-otp-input /></div>
                <p class="sa-otp-note" id="countdown-container">OTP terakhir dikirim <span id="last_otp_at"></span>. Kirim ulang tersedia dalam <strong id="countdown">-:-</strong>.</p>
                <p class="sa-otp-note d-none" id="resend-otp-container">Tidak mendapatkan kode? <button type="button" id="resend-otp">Kirim Ulang</button></p>
                @if (config('recaptcha.enabled'))
                    <div class="sa-login-captcha" id="register-recaptcha">{!! htmlFormSnippet() !!}</div>
                @endif
            </div>
            <div class="modal-footer"><button class="sa-login-submit" id="register" type="button">Verifikasi dan Buat Akun</button></div>
        </div>
    </div>
</div>
@endsection

@vite(['resources/js/pages/register.js'])

@push('scripts')
    @vite(['resources/js/landing/login3d.js'])
@endpush
