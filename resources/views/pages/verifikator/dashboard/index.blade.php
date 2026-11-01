@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading fx-dash">
        <div class="fx-dash__title">
            <div>
                <span class="fx-kicker">Ringkasan Verifikator</span>
                <h1>Dashboard</h1>
                <p>Selamat datang, {{ auth()->user()->name ?? 'Verifikator' }}. Tinjau usulan yang masuk dan lanjutkan proses verifikasi.</p>
            </div>
            <div class="fx-dash__title-actions">
                <a class="btn btn-primary fx-btn-pill" href="{{ route('verifikator.verifikasi.index') }}"><i class="isax-bold isax-shield-tick"></i>Mulai Verifikasi</a>
                <a class="btn fx-btn-dark fx-btn-pill" href="{{ route('verifikator.permohonan.index') }}"><i class="isax-bold isax-receipt"></i>Semua Permohonan</a>
            </div>
        </div>
        <section class="fx-kpis fx-kpis--2">
            <article class="fx-kpi fx-kpi--red">
                <span class="fx-kpi__icon"><i class="isax-bold isax-bag"></i></span>
                <div><span>Usulan Masuk</span><strong>{{ number_format($total_permohonan) }}</strong><small>Total usulan yang perlu ditangani</small></div>
            </article>
            <article class="fx-kpi">
                <span class="fx-kpi__icon"><i class="isax-bold isax-tick-circle"></i></span>
                <div><span>Usulan Selesai</span><strong>{{ number_format($permohonan_selesai) }}</strong><small>Usulan yang telah diselesaikan</small></div>
            </article>
        </section>
        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="card fx-table-card">
                            <div class="card-header">
                                <h4 class="card-title
                                             mb-0">Usulan Terbaru</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm"
                                           id="permohonan-table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Jenis Permohonan</th>
                                                <th>Nomor Registrasi</th>
                                                <th>Tanggal Pengajuan</th>
                                                <th>Nama Pemohon</th>
                                                {{-- <th>Pengantar Permohonan</th> --}}
                                                <th>Lampiran Teknis</th>
                                                <th>Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#permohonan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('verifikator.permohonan.table') }}',
                    data: function(d) {
                        d.search = $('#dt-search-0').val();
                        d.status = $('#status').val();
                        d.jenis_izin_id = $('#jenis_izin_id').val();
                    }
                },
                order: [
                    [3, 'desc']
                ],
                columns: [{
                        data: null,
                        name: 'index',
                        searchable: false,
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'nama_jenis_izin',
                        name: 'nama_jenis_izin',
                        orderable: false
                    },
                    {
                        data: 'nomor_registrasi',
                        name: 'nomor_registrasi',
                        orderable: false
                    },
                    {
                        data: 'waktu_pengajuan',
                        name: 'waktu_pengajuan'
                    },
                    {
                        data: 'nama_pemohon',
                        name: 'nama_pemohon',
                        orderable: false
                    },
                    // {
                    //     data: 'surat_permohonan_rekomendasi',
                    //     name: 'surat_permohonan_rekomendasi',
                    //     className: 'text-center',
                    //     render: function(data, type, row) {
                    //         return data ?
                    //             `<span class='text-success'><i class='isax-bold isax-tick-circle'></i></span>` :
                    //             '-';
                    //     },
                    //     orderable: false
                    // },
                    {
                        data: 'surat_rekomendasi',
                        name: 'surat_rekomendasi',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return data ?
                                `<span class='text-success'><i class='isax-bold isax-tick-circle'></i></span>` :
                                '-';
                        },
                        orderable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'status_badge',
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center',
                        orderable: false
                    }
                ],
            });
        });
    </script>
@endpush
