@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">Buku Tamu</h2>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-primary text-xsm fw-bold mb-1">Hari Ini</p>
                            <h3 class="mb-0">{{ number_format($summary['today']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-primary text-xsm fw-bold mb-1">7 Hari Terakhir</p>
                            <h3 class="mb-0">{{ number_format($summary['last_7_days']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-primary text-xsm fw-bold mb-1">Sebulan Terakhir</p>
                            <h3 class="mb-0">{{ number_format($summary['last_month']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.laporan.buku-tamu.export') }}"
                  method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                       class_input="text-xsm"
                                                       id="periode"
                                                       label="Periode"
                                                       name="periode"
                                                       type="daterange"
                                                       value="{{ old('periode') }}" />
                        <button class="btn btn-primary d-block w-100"
                                type="submit"><i class="isax isax-document-download me-1"></i> Export</button>
                    </div>
                </div>
            </form>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm"
                               id="buku-tamu-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Nomor Telepon</th>
                                    <th>Alamat</th>
                                    <th>Tanggal Kunjungan</th>
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
            $('#buku-tamu-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.laporan.buku-tamu.table') }}',
                    data: function(d) {
                        d.search = $('#dt-search-0').val();
                    }
                },
                order: [
                    [5, 'desc']
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
                        data: 'nama',
                        name: 'nama',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'email',
                        name: 'email',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'telepon',
                        name: 'telepon',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'alamat',
                        name: 'alamat',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                ],
            });
        });
    </script>
@endpush
