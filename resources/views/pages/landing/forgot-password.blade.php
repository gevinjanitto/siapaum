@extends('layouts.landing.auth-base')

@section('content')
<main class="sa-login-page sa-forgot-page tb-login">
    <section class="sa-login-visual tb-gate" data-testid="forgot-gate-visual">
        <a class="sa-login-back" href="{{ route('login.index') }}" aria-label="Kembali ke halaman login" data-testid="forgot-back-btn"><i class="isax isax-arrow-left-2"></i></a>
        <span class="tb-gate__brand"><img src="{{ asset('assets/images/siap-aum/siap-aum-mark.svg') }}" alt=""> SIAP AUM</span>
        <div class="tb-gate__bg" aria-hidden="true"><span></span><span></span></div>
        <canvas id="loginGate" data-ornament="{{ asset('assets/images/siap-aum/gate-ornament.jpg') }}" aria-label="Gapura 3D Kabupaten Tabanan" data-testid="forgot-gate-canvas"></canvas>
        <img class="tb-gate__fallback" src="{{ asset('assets/images/siap-aum/figma-raw-4.webp') }}" alt="Gapura Kabupaten Tabanan">
        <div class="tb-gate__hint" data-testid="forgot-gate-hint"><i class="isax isax-sms"></i> Klik email untuk membuka palang</div>
        <div class="tb-gate__intro">
            <h1>Lupa password? <em>Tenang saja</em></h1>
            <p>Kami akan mengirimkan tautan pemulihan agar Anda dapat membuat password baru dengan aman.</p>
        </div>
    </section>

    <section class="sa-login-panel">
        <div class="sa-login-card sa-forgot-card tb-login-card">
            <div class="sa-login-card__heading">
                <span class="sa-eyebrow"><i></i>Lupa Password</span>
                <h2>Atur ulang password</h2>
                <p>Masukkan email yang terdaftar. Tautan untuk membuat password baru akan dikirimkan ke email tersebut.</p>
            </div>

            <form action="{{ route('forgot-password.store') }}" method="POST" data-testid="forgot-form">
                @csrf
                <div class="sa-login-field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Masukkan email terdaftar" autocomplete="email" required data-testid="forgot-email-input">@error('email')<span data-testid="forgot-email-error">{{ $message }}</span>@enderror</div>
                @if (config('recaptcha.enabled'))
                    <div class="sa-login-captcha" data-testid="forgot-captcha">{!! htmlFormSnippet() !!}</div>
                    @error('g-recaptcha-response')<span class="sa-form-error" data-testid="forgot-captcha-error">{{ $message }}</span>@enderror
                @endif
                <button class="sa-login-submit" type="submit" data-testid="forgot-submit-btn">Kirim Tautan Pemulihan <i class="isax isax-arrow-right-3"></i></button>
                <p class="sa-login-register">Ingat password Anda? <a href="{{ route('login.index') }}" data-testid="forgot-login-link">Kembali ke Login</a></p>
                <div class="sa-login-partners"><img src="{{ asset('assets/images/maiharta-blue.png') }}" alt="Maiharta"><img src="{{ asset('assets/images/bsre.png') }}" alt="BSrE"></div>
            </form>
        </div>
    </section>
</main>
@endsection

@push('scripts')
    @vite(['resources/js/landing/login3d.js'])
@endpush
