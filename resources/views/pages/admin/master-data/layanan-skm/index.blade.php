@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">Layanan SKM</h2>
                    <a class="ms-4 btn btn-primary rounded"
                       href="{{ route('admin.master-data.layanan-skm.group-skm.create') }}"><i class="isax isax-element-plus"></i>
                        Tambah Group Layanan SKM</a>
                    <a class="ms-4 btn btn-primary rounded"
                       href="{{ route('admin.master-data.layanan-skm.create') }}"><i class="isax isax-element-plus"></i>
                        Tambah Layanan SKM</a>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Group SKM</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm"
                                       id="group-skm-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Icon</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Layanan SKM</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm"
                                       id="layanan-skm-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Nama Group</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
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
            $('#group-skm-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.master-data.layanan-skm.group-skm-table') }}',
                    data: function(d) {
                        d.search = $('#group-skm-table #dt-search-0').val();
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
                    //image
                    {
                        data: 'image_url',
                        name: 'image_url',
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!data) {
                                return `-`;
                            }else{
                                return `<img src="${data}" alt="${row.nama}" class="img-fluid"
                                            style="max-width: 50px;">`;
                            }
                        }
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center fs-6 d-flex justify-content-center align-items-center gap-3',
                        orderable: false,
                    },
                ],
            });

            $('#layanan-skm-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.master-data.layanan-skm.table') }}',
                    data: function(d) {
                        d.search = $('#layanan-skm-table #dt-search-0').val();
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
                        data: 'nama_group',
                        name: 'nama_group',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center fs-6 d-flex justify-content-center align-items-center gap-3',
                        orderable: false,
                    },
                ],
            });
        });
    </script>
@endpush
