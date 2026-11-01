@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">Verifikasi</h2>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="d-flex mb-3">
                <x-dashboard.input-filter-select :options="$jenis_izins->map(fn($j) => $j->only('id', 'nama'))->pluck('nama', 'id')"
                                                 allowClear="true"
                                                 id="jenis_izin_id"
                                                 name="jenis_izin_id"
                                                 placeholder="Pilih jenis izin" />
            </div>
            <div class="card">
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
                                    <th>Status Berkas</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                        </table>
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
                    url: '{{ route('verifikator.verifikasi.table') }}',
                    data: function(d) {
                        d.search = $('#dt-search-0').val();
                        d.jenis_izin_id = $('#jenis_izin_id').val();
                    }
                },
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
                        name: 'waktu_pengajuan',
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
                    //         return data ? `<span class='text-success'><i class='isax-bold isax-tick-circle'></i></span>` : '-';
                    //     },
                    //     orderable: false
                    // },
                    {
                        data: 'surat_rekomendasi',
                        name: 'surat_rekomendasi',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return data ? `<span class='text-success'><i class='isax-bold isax-tick-circle'></i></span>` : '-';
                        },
                        orderable: false
                    },
                    {
                        data: 'status_berkas',
                        name: 'status_berkas',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return data ? `<span class='text-success'><i class='isax-bold isax-tick-circle'></i></span>` : `-`;
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

            $('#status-container button').on('click', function() {
                // add fw-bold class to the clicked button
                $('#status-container button').removeClass('fw-bold text-primary');
                $(this).addClass('fw-bold text-primary');

                $('#status').val($(this).data('value'));
                $('#permohonan-table').DataTable().ajax.reload();
            });

            $('#jenis_izin_id').on('change', function() {
                $('#permohonan-table').DataTable().ajax.reload();
            });
        });
    </script>
@endpush
