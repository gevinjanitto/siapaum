@extends('layouts.dashboard.dashboard-base')

@php
    $lainnya = max($total_permohonan - $permohonan_diproses - $permohonan_selesai, 0);
    $pct = fn($v) => $total_permohonan > 0 ? round(($v / $total_permohonan) * 100) : 0;
@endphp

@section('content')
    <div class="page-heading fx-dash">
        <div class="fx-dash__title">
            <div>
                <span class="fx-kicker">Ringkasan Administrator</span>
                <h1>Alur Perizinan</h1>
                <p>Selamat datang, {{ auth()->user()->name ?? 'Admin' }}. Pantau permohonan dan kelola layanan SIAP AUM dari satu halaman.</p>
            </div>
            <div class="fx-dash__title-actions">
                <span class="fx-chip"><i class="isax isax-shield-tick"></i>Sistem berjalan normal</span>
                <a class="btn btn-primary fx-btn-pill" href="{{ route('admin.permohonan.index') }}">
                    <i class="isax-bold isax-receipt-search"></i>Lihat Permohonan
                </a>
                <button type="button" class="btn fx-btn-dark fx-btn-pill" id="deployBtn">
                    <i class="isax isax-refresh"></i>Update Aplikasi
                </button>
            </div>
        </div>

        <section class="fx-panel fx-flow" aria-label="Statistik permohonan">
            <div class="fx-panel__head">
                <h2>Tahapan Permohonan</h2>
                <div class="fx-avatars" aria-label="Pemohon terbaru">
                    @forelse ($permohonan_terbaru->take(7) as $p)
                        <span title="{{ $p->nama }}" style="--i: {{ $loop->index }}">{{ strtoupper(substr($p->nama ?? 'P', 0, 1)) }}</span>
                    @empty
                        <span class="is-empty"><i class="isax isax-profile-2user"></i></span>
                    @endforelse
                </div>
                <div class="fx-panel__tools">
                    <a class="fx-icon-btn" href="{{ route('admin.permohonan.index') }}" title="Permohonan"><i class="isax isax-receipt"></i></a>
                    <a class="fx-icon-btn" href="{{ route('admin.laporan.rekap-permohonan.index') }}" title="Laporan"><i class="isax isax-export-1"></i></a>
                    <a class="fx-icon-btn" href="{{ route('admin.laporan.ijin-terbit-bulanan.index') }}" title="Ijin terbit bulanan"><i class="isax isax-calendar-1"></i></a>
                </div>
            </div>

            <div class="fx-flow__board">
                <div class="fx-flow__col">
                    <article class="fx-stage fx-stage--total">
                        <div class="fx-stage__icon"><i class="isax-bold isax-document-text"></i></div>
                        <span class="fx-stage__label">Total Permohonan</span>
                        <strong>{{ number_format($total_permohonan, 0, ',', '.') }}</strong>
                        <small>Usulan yang telah diajukan</small>
                        <div class="fx-stage__bar"><i style="width: 100%"></i></div>
                    </article>
                    <span class="fx-flow__caption">Permohonan Masuk</span>
                </div>
                <div class="fx-flow__col">
                    <article class="fx-stage fx-stage--process">
                        <div class="fx-stage__icon"><i class="isax-bold isax-setting-2"></i></div>
                        <span class="fx-stage__label">Sedang Diproses</span>
                        <strong>{{ number_format($permohonan_diproses, 0, ',', '.') }}</strong>
                        <small>Memerlukan tindak lanjut</small>
                        <div class="fx-stage__bar"><i style="width: {{ $pct($permohonan_diproses) }}%"></i></div>
                    </article>
                    <span class="fx-flow__caption">Verifikasi</span>
                </div>
                <div class="fx-flow__col">
                    <article class="fx-stage fx-stage--done">
                        <div class="fx-stage__icon"><i class="isax-bold isax-tick-circle"></i></div>
                        <span class="fx-stage__label">Permohonan Selesai</span>
                        <strong>{{ number_format($permohonan_selesai, 0, ',', '.') }}</strong>
                        <small>Telah diselesaikan</small>
                        <div class="fx-stage__bar"><i style="width: {{ $pct($permohonan_selesai) }}%"></i></div>
                    </article>
                    <span class="fx-flow__caption">Izin Terbit</span>
                </div>
                <div class="fx-flow__col">
                    <div class="fx-tiles">
                        <a class="is-active" href="{{ route('admin.master-data.user.index') }}"><i class="isax-bold isax-people"></i><span>Master User</span></a>
                        <a href="{{ route('admin.master-data.jenis-izin.index') }}"><i class="isax-bold isax-folder-open"></i><span>Jenis Izin</span></a>
                        <a href="{{ route('admin.master-data.layanan-skm.index') }}"><i class="isax-bold isax-message-question"></i><span>Layanan SKM</span></a>
                        <a href="{{ route('admin.laporan.rekap-permohonan.index') }}"><i class="isax-bold isax-chart-2"></i><span>Laporan</span></a>
                    </div>
                    <span class="fx-flow__caption">Akses Cepat</span>
                </div>
            </div>
        </section>

        <div class="fx-dash__grid">
            <section class="fx-panel fx-recent">
                <div class="fx-panel__head">
                    <div>
                        <span class="fx-kicker">Aktivitas Terbaru</span>
                        <h2>Permohonan terkini</h2>
                    </div>
                    <a class="fx-link" href="{{ route('admin.permohonan.index') }}">Lihat semua <i class="isax isax-arrow-right-3"></i></a>
                </div>
                <div class="fx-recent__head" aria-hidden="true">
                    <span>Pemohon</span><span>Status</span><span>Tanggal</span><span></span>
                </div>
                @forelse ($permohonan_terbaru as $permohonan)
                    <a class="fx-recent__row" href="{{ route('admin.permohonan.show', $permohonan->id) }}">
                        <span class="fx-recent__who">
                            <span class="fx-recent__avatar">{{ strtoupper(substr($permohonan->nama ?? 'P', 0, 1)) }}</span>
                            <span>
                                <strong>{{ $permohonan->nama ?? 'Pemohon' }}</strong>
                                <small>{{ $permohonan->nama_jenis_izin }}</small>
                            </span>
                        </span>
                        <span class="fx-recent__status">{!! $permohonan->status_badge !!}</span>
                        <span class="fx-recent__date">{{ ($permohonan->pengajuan_at ?? $permohonan->created_at)?->locale('id')->isoFormat('D MMM YYYY') }}</span>
                        <i class="isax isax-arrow-right-3 fx-recent__arrow"></i>
                    </a>
                @empty
                    <div class="fx-empty">
                        <span><i class="isax isax-document"></i></span>
                        <strong>Belum ada permohonan</strong>
                        <p>Permohonan terbaru akan tampil di bagian ini.</p>
                    </div>
                @endforelse
            </section>

            <section class="fx-panel fx-donut-card">
                <div class="fx-panel__head">
                    <div>
                        <span class="fx-kicker">Tingkat Penyelesaian</span>
                        <h2>Perjalanan Permohonan</h2>
                    </div>
                </div>
                <div class="fx-donut" style="--done: {{ $pct($permohonan_selesai) }}; --proc: {{ $pct($permohonan_diproses) }}">
                    <div class="fx-donut__center">
                        <strong>{{ $persentase_selesai }}%</strong>
                        <span>Selesai</span>
                    </div>
                </div>
                <ul class="fx-legend">
                    <li><i class="is-done"></i>Selesai<b>{{ number_format($permohonan_selesai, 0, ',', '.') }}</b></li>
                    <li><i class="is-proc"></i>Diproses<b>{{ number_format($permohonan_diproses, 0, ',', '.') }}</b></li>
                    <li><i class="is-other"></i>Lainnya<b>{{ number_format($lainnya, 0, ',', '.') }}</b></li>
                </ul>
                <p class="fx-donut-card__note">{{ number_format($permohonan_selesai, 0, ',', '.') }} dari {{ number_format($total_permohonan, 0, ',', '.') }} permohonan telah selesai.</p>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('deployBtn').addEventListener('click', function() {
            if (!confirm('Apakah Anda yakin ingin memperbarui aplikasi? Proses ini akan mengambil kode terbaru dari repository.')) {
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>Memperbarui...';

            fetch('{{ route('admin.deploy') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    location.reload();
                    return;
                }

                btn.disabled = false;
                btn.innerHTML = '<i class="isax isax-refresh"></i>Update Aplikasi';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui aplikasi');
                btn.disabled = false;
                btn.innerHTML = '<i class="isax isax-refresh"></i>Update Aplikasi';
            });
        });
    </script>
@endpush
