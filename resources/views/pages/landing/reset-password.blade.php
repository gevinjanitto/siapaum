@extends('layouts.landing.auth-base')

@section('content')
<main class="sa-login-page sa-reset-page tb-login">
    <section class="sa-login-visual tb-gate" data-testid="reset-gate-visual">
        <a class="sa-login-back" href="{{ route('login.index') }}" aria-label="Kembali ke halaman login" data-testid="reset-back-btn"><i class="isax isax-arrow-left-2"></i></a>
        <span class="tb-gate__brand"><img src="{{ asset('assets/images/siap-aum/siap-aum-mark.svg') }}" alt=""> SIAP AUM</span>
        <div class="tb-gate__bg" aria-hidden="true"><span></span><span></span></div>
        <canvas id="loginGate" data-ornament="{{ asset('assets/images/siap-aum/gate-ornament.jpg') }}" aria-label="Gapura 3D Kabupaten Tabanan" data-testid="reset-gate-canvas"></canvas>
        <img class="tb-gate__fallback" src="{{ asset('assets/images/siap-aum/figma-raw-4.webp') }}" alt="Gapura Kabupaten Tabanan">
        <div class="tb-gate__hint" data-testid="reset-gate-hint"><i class="isax isax-lock"></i> Klik password untuk membuka palang</div>
        <div class="tb-gate__intro">
            <h1>Buat password <em>baru Anda</em></h1>
            <p>Gunakan password yang kuat dan mudah Anda ingat untuk menjaga keamanan akun.</p>
        </div>
    </section>

    <section class="sa-login-panel">
        <div class="sa-login-card sa-register-card tb-login-card">
            <div class="sa-login-card__heading">
                <span class="sa-eyebrow"><i></i>Reset Password</span>
                <h2>Atur password baru</h2>
                <p>Password baru akan digunakan untuk akun <strong data-testid="reset-email-label">{{ $email }}</strong>.</p>
            </div>
            <form action="{{ route('password.update', $token) }}" method="POST" data-testid="reset-form">
                @csrf
                <input name="token" type="hidden" value="{{ $token }}">
                <input name="email" type="hidden" value="{{ $email }}">
                <div class="sa-login-field"><label for="password">Password baru</label><div><input id="password" name="password" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required data-testid="reset-password-input"><button class="btn-toggle-password isax isax-eye-slash" type="button" aria-label="Tampilkan password" data-testid="reset-toggle-password"></button></div>@error('password')<span data-testid="reset-password-error">{{ $message }}</span>@enderror</div>
                <div class="sa-login-field"><label for="password_verify">Konfirmasi password</label><div><input id="password_verify" name="password_verify" type="password" placeholder="Masukkan kembali password" autocomplete="new-password" required data-testid="reset-password-verify-input"><button class="btn-toggle-password isax isax-eye-slash" type="button" aria-label="Tampilkan konfirmasi password" data-testid="reset-toggle-password-verify"></button></div>@error('password_verify')<span data-testid="reset-password-verify-error">{{ $message }}</span>@enderror</div>
                @error('email')<span class="sa-form-error" data-testid="reset-email-error">{{ $message }}</span>@enderror
                <button class="sa-login-submit" type="submit" data-testid="reset-submit-btn">Simpan Password Baru <i class="isax isax-arrow-right-3"></i></button>
                <p class="sa-login-register">Ingat password Anda? <a href="{{ route('login.index') }}" data-testid="reset-login-link">Kembali ke Login</a></p>
                <div class="sa-login-partners"><img src="{{ asset('assets/images/maiharta-blue.png') }}" alt="Maiharta"><img src="{{ asset('assets/images/bsre.png') }}" alt="BSrE"></div>
            </form>
        </div>
    </section>
</main>
@endsection

@push('scripts')
    @vite(['resources/js/landing/login3d.js'])
@endpush
