@extends('layouts.landing.main-base')

@section('content')
<main class="siap-aum-landing fx-landing">
    <section class="lx-hero" id="home" data-testid="landing-hero">
        <div class="lx-hero__bg" aria-hidden="true"><span class="lx-hero__glow"></span><span class="lx-hero__grid"></span><span class="lx-hero__orb lx-hero__orb--a"></span><span class="lx-hero__orb lx-hero__orb--b"></span></div>
        <div class="lx-hero__copy">
            <div class="lx-hero__badge" aria-label="Pemerintah Kabupaten Tabanan" data-testid="hero-badge">
                <span class="lx-hero__logos">
                    <img src="{{ asset('assets/images/siap-aum/siap-aum-mark.svg') }}" alt="Logo SIAP AUM">
                    <img src="{{ asset('assets/images/siap-aum/figma-raw-3.png') }}" alt="Lambang Kabupaten Tabanan">
                </span>
                <span class="lx-live"><i></i>Online 24/7</span>
                <span class="tb-badge-text"><span class="tb-badge-long">Portal Perizinan </span>Kabupaten Tabanan</span>
            </div>
            <h1 data-split>Sistem Informasi Administrasi Perizinan <em>Andal, Unggul,</em> Nyaman, dan Gesit</h1>
            <p>SIAP AUM adalah portal pelayanan perizinan online berbasis website yang dikembangkan oleh Pemerintah Kabupaten Tabanan yang bertujuan untuk memenuhi kebutuhan masyarakat dalam pengajuan izin yang transparan, inovatif, efektif, dan efisien. Masyarakat dapat mengajukan permohonan izin secara mandiri kapan pun dan di mana pun tanpa harus datang langsung ke kantor DPMPTSP Kabupaten Tabanan.</p>
            <div class="lx-hero__actions">
                @auth
                    <a class="sa-btn sa-btn--primary" data-magnetic data-testid="hero-dashboard-btn" href="{{ route('dashboard') }}">Dashboard <i class="isax isax-arrow-right-3"></i></a>
                @else
                    <a class="sa-btn sa-btn--primary" data-magnetic data-testid="hero-login-btn" href="{{ route('login.index') }}">Login <i class="isax isax-arrow-right-3"></i></a>
                @endauth
                <a class="sa-btn sa-btn--light" data-magnetic data-testid="hero-check-btn" href="#cek-permohonan"><i class="isax isax-search-normal-1"></i> Cek Permohonan</a>
            </div>
            <div class="lx-hero__mini-stats">
                <div data-spot><strong data-count="{{ $permohonan_count }}" data-testid="hero-stat-permohonan">{{ number_format($permohonan_count) }}</strong><span>Permohonan {{ $tahun }}</span></div>
                <div data-spot><strong data-count="{{ $permohonan_selesai_count }}">{{ number_format($permohonan_selesai_count) }}</strong><span>Izin Selesai</span></div>
                <div data-spot><strong data-count="{{ $user_count }}">{{ number_format($user_count) }}</strong><span>Pemohon</span></div>
            </div>
        </div>
        <div class="lx-stage" data-testid="hero-3d-stage">
            <canvas id="gate3d" data-ornament="{{ asset('assets/images/siap-aum/gate-ornament.jpg') }}" aria-label="Model 3D Gerbang Kabupaten Tabanan"></canvas>
            <img class="lx-stage__fallback" src="{{ asset('assets/images/siap-aum/figma-raw-4.webp') }}" alt="Gerbang Kabupaten Tabanan">
            <div class="lx-chip lx-chip--flow tb-flow" aria-hidden="true" data-testid="hero-flow-chip">
                <span class="lx-chip__title">Alur Permohonan</span>
                <div class="tb-flow__row">
                    <div class="lx-chip__step is-done"><i class="isax-bold isax-document-upload"></i><span>Pengajuan</span><b class="isax isax-tick-circle"></b></div>
                    <span class="tb-flow__line"></span>
                    <div class="lx-chip__step is-done"><i class="isax-bold isax-shield-search"></i><span>Verifikasi</span><b class="isax isax-tick-circle"></b></div>
                    <span class="tb-flow__line"></span>
                    <div class="lx-chip__step is-active"><i class="isax-bold isax-document-text"></i><span>Izin terbit</span><b class="lx-dots"><em></em><em></em><em></em></b></div>
                </div>
            </div>
            <div class="lx-chip lx-chip--tte" aria-hidden="true">
                <span class="lx-chip__dot"><i class="isax-bold isax-verify"></i></span>
                <div><strong>Tanda Tangan Elektronik</strong><small>Dokumen resmi &amp; sah</small></div>
            </div>
        </div>
        <a class="lx-scroll" href="#tentang" data-testid="hero-scroll-link"><span></span>Scroll</a>
    </section>
    <div class="lx-marquee" aria-hidden="true">
        <div class="lx-marquee__track">
            @for ($m = 0; $m < 2; $m++)
                <span>Andal</span><i></i><span>Unggul</span><i></i><span>Nyaman</span><i></i><span>Gesit</span><i></i><span>Tanda Tangan Elektronik</span><i></i><span>Tanpa Calo</span><i></i><span>Pantau Real-time</span><i></i>
            @endfor
        </div>
    </div>

    <section class="fx-section fx-about" id="tentang">
        <div class="tb-about__visual" data-reveal data-testid="about-visual">
            <figure class="tb-frame">
                <img src="{{ asset('assets/images/siap-aum/jatiluwih.webp') }}" alt="Sawah terasering Jatiluwih, Kabupaten Tabanan" width="1500" height="999" loading="lazy" decoding="async" data-parallax="0.12">
                <figcaption><i class="isax-bold isax-location"></i><span><strong>Jatiluwih</strong> Penebel, Tabanan</span></figcaption>
            </figure>
            <svg class="tb-badge" viewBox="0 0 120 120" aria-hidden="true">
                <defs><path id="tb-badge-path" d="M60 60 m-46 0 a46 46 0 1 1 92 0 a46 46 0 1 1 -92 0"/></defs>
                <text textLength="284" lengthAdjust="spacing"><textPath href="#tb-badge-path">SIAP AUM TABANAN &#8226;</textPath></text>
            </svg>
            <span class="tb-badge__core" aria-hidden="true"><img src="{{ asset('assets/images/siap-aum/siap-aum-mark.svg') }}" alt=""></span>
            <div class="tb-about__card" aria-hidden="true">
                <span class="tb-about__card-icon"><i class="isax-bold isax-document-text"></i></span>
                <div><strong>Izin diterbitkan</strong><small>Ditandatangani elektronik</small></div>
                <b class="isax-bold isax-tick-circle"></b>
            </div>
        </div>
        <div class="fx-about__copy">
            <span class="sa-eyebrow"><i></i>Tentang</span>
            <h2 data-reveal data-split="reveal">Mari daftarkan permohonan izin anda melalui SIAP AUM</h2>
            <p class="fx-about__intro" data-reveal>Dengan menggunakan SIAP AUM, Anda dapat mengajukan permohonan izin secara mandiri dengan sistem online. Berikut merupakan keunggulan dari SIAP AUM:</p>
            <ul class="fx-benefits">
                <li data-reveal data-spot><span><i class="isax-bold isax-user-tick"></i></span>Memudahkan anda mengajukan permohonan izin tanpa calo</li>
                <li data-reveal data-spot><span><i class="isax-bold isax-shield-search"></i></span>Verifikasi berkas oleh petugas DPMPTSP secara online</li>
                <li data-reveal data-spot><span><i class="isax-bold isax-location-tick"></i></span>Pemantauan posisi berkas secara online</li>
                <li data-reveal data-spot><span><i class="isax-bold isax-edit-2"></i></span>Revisi berkas permohonan izin secara online</li>
            </ul>
        </div>
    </section>

    <section class="fx-recap" id="rekap">
        <div class="fx-recap__head">
            <div class="sa-section-heading--inverse">
                <span class="sa-eyebrow"><i></i>Rekap</span>
                <h2>Rekapitulasi Izin yang Masuk</h2>
            </div>
            <form class="sa-year-filter fx-year" action="{{ route('home') }}" method="GET"><label for="tahun">Tahun</label><select id="tahun" name="tahun" onchange="this.form.submit()">@foreach ($tahun_options as $tahun_option)<option value="{{ $tahun_option }}" @selected($tahun_option == $tahun)>{{ $tahun_option }}</option>@endforeach</select></form>
        </div>
        @php
            $stats = [
                ['icon' => 'stat-user.svg', 'value' => $user_count, 'label' => 'Jumlah Pemohon'],
                ['icon' => 'stat-graph.svg', 'value' => $permohonan_count, 'label' => 'Total Permohonan'],
                ['icon' => 'stat-timer.svg', 'value' => $permohonan_proses_count, 'label' => 'Dalam Proses'],
                ['icon' => 'stat-printer.svg', 'value' => $permohonan_selesai_count, 'label' => 'Izin Selesai'],
            ];
        @endphp
        <div class="fx-stats">
            @foreach ($stats as $stat)
                <article class="fx-stat" data-reveal data-spot data-tilt><span class="fx-stat__icon"><img src="{{ asset('assets/images/siap-aum/'.$stat['icon']) }}" alt="" loading="lazy" decoding="async"></span><span class="fx-stat__label">{{ $stat['label'] }}</span><strong data-count="{{ $stat['value'] }}">{{ number_format($stat['value']) }}</strong></article>
            @endforeach
        </div>
        <details class="sa-survey fx-survey"><summary><i class="isax isax-medal-star"></i> Hasil Survey Kepuasan Masyarakat <i class="isax isax-arrow-down-1"></i></summary><div class="sa-survey__body"><div class="sa-survey__score"><strong>{{ number_format((float) min($laporan_survey['persentaseNilaiIKM'], 84), 2, '.', '') }}</strong><span>Sangat Baik · {{ $laporan_survey['totalKuesioner'] }} responden</span></div><div class="sa-survey__bars">@foreach ($laporan_survey['nrrTertbgUnsurPercentage'] as $layanan => $value)<div><span>{{ $layanan }}</span><i style="width: {{ min($value, 84) }}%"></i></div>@endforeach</div></div></details>
    </section>

    <section class="fx-section fx-guide" id="panduan">
        <div class="fx-heading">
            <span class="sa-eyebrow"><i></i>Panduan</span>
            <h2 data-split="reveal">Panduan Pengajuan</h2>
            <p>Enam langkah sederhana dari pendaftaran akun hingga izin Anda disetujui.</p>
        </div>
        @php
            $steps = [
                ['user-add', 'Daftarkan akun anda untuk mengakses halaman permohonan'],
                ['login', 'Masuklah ke akun Anda untuk memulai permohonan.'],
                ['add-square', 'Buka tombol Tambah untuk membuat pengajuan.'],
                ['document-text', 'Pilih jenis pengajuan yang ingin Anda buat.'],
                ['clipboard-text', 'Isilah formulir yang telah disediakan dengan saksama.'],
                ['tick-circle', 'Tunggulah hingga pengajuan Anda disetujui.'],
            ];
        @endphp
        <div class="fx-steps">
            @foreach ($steps as $index => $step)
                <a class="fx-step" href="{{ route('user-guide') }}" data-reveal data-spot data-tilt data-testid="guide-step-{{ $index + 1 }}">
                    <div class="fx-step__top"><span class="fx-step__icon"><i class="isax isax-{{ $step[0] }}"></i></span><b>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</b></div>
                    <p>{{ $step[1] }}</p>
                </a>
            @endforeach
        </div>
    </section>

    <section class="fx-section fx-check" id="cek-permohonan">
        <div class="fx-check__card" data-reveal data-spot>
            <div>
                <span class="sa-eyebrow"><i></i>Cek Permohonan</span>
                <h2>Cek Permohonan</h2>
                <p>Masukkan nomor permohonan untuk melihat posisi berkas dan riwayat prosesnya.</p>
            </div>
            <form class="fx-check__form" action="{{ route('cek-permohonan.store') }}" method="POST">@csrf<label class="visually-hidden" for="nomor_registrasi">Nomor permohonan</label><i class="isax isax-receipt-search"></i><input data-testid="check-permohonan-input" id="nomor_registrasi" name="nomor_registrasi" type="text" required placeholder="cek Nomor Permohonan"><button type="submit" data-magnetic data-testid="check-permohonan-submit" aria-label="Cari nomor permohonan"><span>Cari</span><i class="isax isax-search-normal-1"></i></button></form>
        </div>
    </section>

    <section class="fx-section fx-why" id="kenapa">
        <div class="fx-heading">
            <span class="sa-eyebrow"><i></i>Kenapa Siap Aum</span>
            <h2 data-split="reveal">Kenapa SIAP AUM?</h2>
        </div>
        <div class="tb-bento" data-testid="why-bento">
            <article data-spot class="tb-card tb-card--wide" data-reveal data-testid="why-card-fast">
                <div class="tb-art tb-art--speed" aria-hidden="true">
                    <div class="tb-speed__chips"><span><i class="isax-bold isax-flash-1"></i>Cepat</span><span><i class="isax-bold isax-gps"></i>Akurat</span><span><i class="isax-bold isax-shield-tick"></i>Aman</span><span><i class="isax-bold isax-timer-1"></i>Real-time</span></div>
                    <div class="tb-speed__bars">
                        <div><span>Pengajuan</span><i style="--w: 92%"></i></div>
                        <div><span>Verifikasi</span><i style="--w: 74%"></i></div>
                        <div><span>Penerbitan</span><i style="--w: 58%"></i></div>
                    </div>
                </div>
                <div class="tb-card__copy"><span class="tb-card__no">01</span><h3>Pelayanan Cepat &amp; Efisien</h3><p>Proses perizinan dirancang sederhana dan terintegrasi sehingga pengajuan dapat dilakukan dengan cepat tanpa prosedur yang berbelit.</p></div>
            </article>
            <article data-spot class="tb-card tb-card--red" data-reveal data-testid="why-card-access">
                <div class="tb-art tb-art--phone" aria-hidden="true">
                    <div class="tb-phone"><span class="tb-phone__notch"></span><div class="tb-phone__screen"><span class="tb-phone__bar"></span><span class="tb-phone__bar tb-phone__bar--s"></span><div class="tb-phone__toast"><i class="isax-bold isax-notification-bing"></i><span><strong>Izin terbit</strong><small>Siap diunduh</small></span></div><span class="tb-phone__btn"><i class="isax isax-document-download"></i></span></div></div>
                </div>
                <div class="tb-card__copy"><span class="tb-card__no">02</span><h3>Akses Mudah Kapan Saja &amp; Di Mana Saja</h3><p>Masyarakat dapat mengajukan dan mengunduh izin secara online tanpa harus datang ke kantor, cukup melalui perangkat masing-masing.</p></div>
            </article>
            <article data-spot class="tb-card" data-reveal data-testid="why-card-secure">
                <div class="tb-art tb-art--doc" aria-hidden="true">
                    <div class="tb-doc"><span class="tb-doc__head"></span><span></span><span></span><span class="tb-doc__s"></span><span></span><span class="tb-doc__s"></span><div class="tb-doc__sign"><em></em></div></div>
                    <div class="tb-seal"><i class="isax-bold isax-verify"></i><b>TTE</b></div>
                </div>
                <div class="tb-card__copy"><span class="tb-card__no">03</span><h3>Aman &amp; Terjamin Legalitasnya</h3><p>Dilengkapi dengan sistem keamanan dan Tanda Tangan Elektronik resmi, sehingga dokumen izin memiliki kekuatan hukum dan terjamin keasliannya.</p></div>
            </article>
            <article data-spot class="tb-card tb-card--wide" data-reveal data-testid="why-card-track">
                <div class="tb-art tb-art--track" aria-hidden="true">
                    <div class="tb-track__head"><span class="lx-live"><i></i>Live</span><strong>REG/2026/••••</strong></div>
                    <div class="tb-track__line"><span class="tb-track__fill"></span>
                        <div class="tb-track__node is-done"><i class="isax-bold isax-document-upload"></i><small>Diajukan</small></div>
                        <div class="tb-track__node is-done"><i class="isax-bold isax-search-status"></i><small>Diperiksa</small></div>
                        <div class="tb-track__node is-now"><i class="isax-bold isax-shield-search"></i><small>Verifikasi</small></div>
                        <div class="tb-track__node"><i class="isax-bold isax-document-text"></i><small>Terbit</small></div>
                    </div>
                </div>
                <div class="tb-card__copy"><span class="tb-card__no">04</span><h3>Transparan &amp; Mudah Dipantau</h3><p>Setiap tahapan permohonan izin dapat dipantau secara real-time, sehingga masyarakat mengetahui status pengajuan dengan jelas.</p></div>
            </article>
        </div>
    </section>

    <section class="fx-section tb-services" aria-roledescription="carousel" aria-label="Tahapan layanan SIAP AUM" data-testid="services-carousel">
        @php
            $service_slides = [
                ['image' => 'service-apply.jpg', 'icon' => 'document-upload', 'title' => 'Ajukan izin secara online', 'description' => 'Ajukan permohonan izin dari mana saja tanpa harus datang ke kantor. Pilih layanan, isi data, dan unggah dokumen secara praktis melalui SIAP AUM.'],
                ['image' => 'service-track.jpg', 'icon' => 'location-tick', 'title' => 'Pantau proses permohonan', 'description' => 'Ketahui perkembangan permohonan secara transparan. Setiap proses verifikasi dan perubahan status dapat dipantau langsung melalui akun Anda.'],
                ['image' => 'service-revise.jpg', 'icon' => 'edit-2', 'title' => 'Perbaiki dokumen dengan mudah', 'description' => 'Jika terdapat dokumen yang perlu diperbaiki, petugas akan memberikan catatan revisi. Perbarui dan kirim kembali berkas tanpa harus datang ke kantor.'],
                ['image' => 'service-download.jpg', 'icon' => 'document-download', 'title' => 'Unduh izin resmi Anda', 'description' => 'Setelah disetujui, dokumen izin resmi dapat diunduh langsung melalui akun SIAP AUM dan dilengkapi Tanda Tangan Elektronik untuk menjamin keasliannya.'],
            ];
        @endphp
        <div class="fx-heading">
            <span class="sa-eyebrow"><i></i>Layanan</span>
            <h2 data-split="reveal">Semua urusan izin dalam satu portal</h2>
        </div>
        <div class="tb-services__wrap" data-reveal>
            <div class="tb-services__media" data-services-media>
                @foreach ($service_slides as $index => $slide)
                    <figure class="tb-services__slide {{ $index === 0 ? 'is-active' : '' }}" aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                        <img src="{{ asset('assets/images/siap-aum/'.$slide['image']) }}" alt="{{ $slide['title'] }}" loading="lazy" decoding="async">
                    </figure>
                @endforeach
                <div class="tb-services__count" aria-hidden="true"><b data-services-no>01</b><span>/ {{ str_pad(count($service_slides), 2, '0', STR_PAD_LEFT) }}</span></div>
                <div class="tb-services__nav">
                    <button type="button" data-carousel-prev aria-label="Slide sebelumnya" data-testid="services-prev-btn"><i class="isax isax-arrow-left-2"></i></button>
                    <button type="button" data-carousel-next aria-label="Slide berikutnya" data-testid="services-next-btn"><i class="isax isax-arrow-right-3"></i></button>
                </div>
            </div>
            <div class="tb-services__list" role="tablist" aria-label="Pilih tahapan layanan">
                @foreach ($service_slides as $index => $slide)
                    <button type="button" role="tab" class="tb-services__tab {{ $index === 0 ? 'is-active' : '' }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}" data-testid="services-tab-{{ $index + 1 }}">
                        <span class="tb-services__icon"><i class="isax isax-{{ $slide['icon'] }}"></i></span>
                        <span class="tb-services__text">
                            <small>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</small>
                            <strong>{{ $slide['title'] }}</strong>
                            <span class="tb-services__desc"><span>{{ $slide['description'] }}</span></span>
                        </span>
                        <i class="tb-services__bar" aria-hidden="true"></i>
                    </button>
                @endforeach
            </div>
        </div>
    </section>
</main>
@endsection
