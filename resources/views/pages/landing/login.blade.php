@extends('layouts.landing.auth-base')

@section('content')
<main class="sa-login-page tb-login">
    <section class="sa-login-visual tb-gate" data-testid="login-gate-visual">
        <a class="sa-login-back" href="{{ route('home') }}" aria-label="Kembali ke beranda" data-testid="login-back-btn"><i class="isax isax-arrow-left-2"></i></a>
        <span class="tb-gate__brand"><img src="{{ asset('assets/images/siap-aum/siap-aum-mark.svg') }}" alt=""> SIAP AUM</span>
        <div class="tb-gate__bg" aria-hidden="true"><span></span><span></span></div>
        <canvas id="loginGate" data-ornament="{{ asset('assets/images/siap-aum/gate-ornament.jpg') }}" aria-label="Gapura 3D Kabupaten Tabanan" data-testid="login-gate-canvas"></canvas>
        <img class="tb-gate__fallback" src="{{ asset('assets/images/siap-aum/figma-raw-4.webp') }}" alt="Gapura Kabupaten Tabanan">
        <div class="tb-gate__hint" data-testid="login-gate-hint"><i class="isax isax-sms"></i> Klik email untuk membuka palang</div>
        <div class="tb-gate__intro">
            <h1>Gerbang digital <em>perizinan Tabanan</em></h1>
            <p data-testid="login-gate-tagline">Sistem Informasi Administrasi Perizinan Andal, Unggul, Nyaman, dan Gesit</p>
        </div>
    </section>
    <section class="sa-login-panel">
        <div class="sa-login-card tb-login-card">
            <div class="sa-login-card__heading"><span class="sa-eyebrow"><i></i>Login</span><h2>Masuk ke akun anda</h2><p>Silakan masukkan email dan password yang telah terdaftar.</p></div>
            <form action="{{ route('login.store') }}" method="POST" data-testid="login-form">
                @csrf
                <div class="sa-login-field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Masukkan email" autocomplete="email" required data-testid="login-email-input">@error('email')<span data-testid="login-email-error">{{ $message }}</span>@enderror</div>
                <div class="sa-login-field"><label for="password">Password</label><div><input id="password" name="password" type="password" placeholder="Masukkan password" autocomplete="current-password" required data-testid="login-password-input"><button class="btn-toggle-password isax isax-eye-slash" type="button" aria-label="Tampilkan password" data-testid="login-toggle-password"></button></div></div>
                @if (config('recaptcha.enabled'))
                    <div class="sa-login-captcha" data-testid="login-captcha">{!! htmlFormSnippet() !!}</div>
                    @error('g-recaptcha-response')<span class="sa-form-error" data-testid="login-captcha-error">{{ $message }}</span>@enderror
                @endif
                <a class="sa-login-forgot" href="{{ route('forgot-password.index') }}" data-testid="login-forgot-link">Lupa Password?</a>
                <button class="sa-login-submit" type="submit" data-testid="login-submit-btn">Masuk <i class="isax isax-arrow-right-3"></i></button>
                <p class="sa-login-register">Belum Punya Akun? <a href="{{ route('register.index') }}" data-testid="login-register-link">Daftar Akun</a></p>
                <div class="sa-login-partners"><img src="{{ asset('assets/images/maiharta-blue.png') }}" alt="Maiharta"><img src="{{ asset('assets/images/bsre.png') }}" alt="BSrE"></div>
            </form>
        </div>
    </section>
</main>
@endsection

@push('scripts')
    @vite(['resources/js/landing/login3d.js'])
@endpush
