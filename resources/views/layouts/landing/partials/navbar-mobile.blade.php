<nav class="navbar navbar-dark navbar-expand d-lg-none fixed-bottom bg-primary">
    <ul class="navbar-nav nav-justified w-100">
        <li class="nav-item">
            <a class="nav-link {{request()->routeIs('home') ? 'active' : ''}}"
               href="/">
                <i class="isax isax-home"></i>
                <span class="d-block">Home</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{request()->routeIs('cek-permohonan*') ? 'active' : ''}}"
            href="/cek-permohonan">
                <i class="isax isax-search-favorite"></i>
                <span class="d-block">Cek Permohonan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{request()->routeIs('user-guide') ? 'active' : ''}}"
               href="/panduan-pengguna">
                <i class="isax isax-document"></i>
                <span class="d-block">Panduan Penggunaan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{request()->routeIs('login') ? 'active' : ''}}"
               href="/login">
                <i class="isax isax-login"></i>
                @auth
                    <span class="d-block">Dashboard</span>
                @else
                    <span class="d-block">Login</span>
                @endauth
            </a>
        </li>
    </ul>
</nav>
