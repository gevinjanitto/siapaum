@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">User</h2>
                    <a href="{{ route('admin.master-data.user.create') }}" class="ms-4 btn btn-primary rounded"><i class="isax isax-element-plus"></i> Tambah</a>
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
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>NIK</th>
                                    <th>Nomor Telepon</th>
                                    <th class="text-center">Role</th>
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
                    url: '{{ route('admin.master-data.user.table') }}',
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
                        data: 'email',
                        name: 'email',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'nik',
                        name: 'nik',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'nomor_telepon',
                        name: 'nomor_telepon',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'role',
                        name: 'role',
                        className: 'text-center',
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
