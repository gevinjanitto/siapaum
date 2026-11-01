@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">Sektor Ijin</h2>
                    <a href="{{ route('admin.master-data.sektor-izin.create') }}" class="ms-4 btn btn-primary rounded"><i class="isax isax-element-plus"></i> Tambah</a>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="izin-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Sektor Ijin</th>
                                    <th>Kategori Ijin</th>
                                    <th>Waktu Pembuatan</th>
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
            $('#izin-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.master-data.sektor-izin.table') }}',
                    data: function(d) {
                        d.search = $('#dt-search-0').val();
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
                        data: 'nama',
                        name: 'nama',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'nama_kategori_izin',
                        name: 'nama_kategori_izin',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center fs-6 d-flex gap-3',
                        orderable: false,
                    },
                ],
            });
        });
    </script>
@endpush
