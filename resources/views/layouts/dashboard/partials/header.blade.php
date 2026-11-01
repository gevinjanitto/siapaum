<header>
    <nav class="navbar navbar-expand navbar-light navbar-top sa-dashboard-navbar fx-topbar">
        <div class="container-fluid">
            <a class="burger-btn d-block" href="#" aria-label="Buka/tutup menu">
                <span class="isax isax-menu-1 fs-4"></span>
            </a>
            <div class="fx-topbar__greet d-none d-md-flex">
                <span>{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                <strong>Halo, {{ \Illuminate\Support\Str::words(auth()->user()->name ?? 'Pengguna', 2, '') }}</strong>
            </div>
            <div class="fx-topbar__actions ms-auto">
                <a class="fx-icon-btn" href="{{ route('home') }}" target="_blank" rel="noopener" title="Halaman depan" aria-label="Halaman depan">
                    <i class="isax isax-global"></i>
                </a>
                <a class="fx-icon-btn d-none d-sm-grid" href="{{ route('profile.index') }}" title="Profil saya" aria-label="Profil saya">
                    <i class="isax isax-setting-2"></i>
                </a>
                <div class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="fx-user-pill">
                        <div class="user-menu d-flex align-items-center">
                            <div class="user-img d-flex align-items-center">
                                <div class="avatar avatar-md">
                                    <img src="{{ auth()->user()->photo_profile }}" alt="">
                                </div>
                            </div>
                            <div class="user-name text-start ms-2 me-2">
                                <h6 class="mb-0">{{ auth()->user()->name ?? auth()->user()->email }}</h6>
                                <p class="mb-0 text-sm">{{ auth()->user()->role->nama }}</p>
                            </div>
                            <i class="isax isax-arrow-down-1 fx-user-pill__chev"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 12rem;">
                        <li>
                            <h6 class="dropdown-header">Halo, {{ auth()->user()->name ?? 'Pengguna' }}</h6>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('profile.index') }}">
                                <i class="icon-mid bi bi-person me-2"></i> Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="icon-mid bi bi-box-arrow-left me-2"></i> Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>
