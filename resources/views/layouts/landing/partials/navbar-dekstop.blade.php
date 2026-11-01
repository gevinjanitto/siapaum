<nav class="sa-navbar fx-nav" aria-label="Navigasi utama">
    <a class="sa-brand" href="{{ route('home') }}"><span class="fx-nav__mark"><img src="{{ asset('assets/images/siap-aum/siap-aum-mark.svg') }}" alt=""></span><span>Siap Aum</span></a>
    <div class="sa-navbar__links">
        <a class="is-active" href="{{ route('home') }}#home">Home</a>
        <a href="{{ route('home') }}#tentang">Tentang</a>
        <a href="{{ route('home') }}#rekap">Rekap</a>
        <a href="{{ route('home') }}#panduan">Panduan</a>
        <a href="{{ route('home') }}#kenapa">Kenapa Siap Aum</a>
        <a href="{{ route('home') }}#kontak">Kontak</a>
    </div>
    @auth
        <a class="sa-navbar__login" href="{{ route('dashboard') }}">Dashboard <i class="isax isax-arrow-right-3"></i></a>
    @else
        <a class="sa-navbar__login" href="{{ route('login.index') }}">Login <i class="isax isax-arrow-right-3"></i></a>
    @endauth
</nav>
