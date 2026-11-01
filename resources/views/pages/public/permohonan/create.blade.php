@extends('layouts.dashboard.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row align-items-center g-2">
                <div class="col-12 col-lg-auto gap-0 order-md-1">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('dashboard') }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a> Pengajuan Ijin
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    {{-- table jenis Ijin --}}
                    <div class="table-responsive">
                        <table class="table table-sm"
                               id="jenis-ijin-table">
                            <thead>
                                <tr>
                                    <th class="d-none">No</th>
                                    <th class="d-none">Jenis Izin</th>
                                    <th class="d-none">Aksi</th>
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
        $('#jenis-ijin-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('public.permohonan.jenis-izin-table') }}',
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
                    name: 'nama'
                },
                {
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return `
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modal${data}"><i class="isax isax-info-circle"></i> Syarat</button>
                            <a href="{{ route('public.permohonan.create') }}/${data}" class="btn btn-primary btn-sm">
                                <i class="isax isax-element-plus"></i> Pengajuan Baru
                            </a>
                            <div class="modal fade" id="modal${data}" tabindex="-1" aria-labelledby="modal${data}Label" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="modal${data}Label">${row.nama}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body text-start">
                                    ${row.deskripsi}
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                        `;
                    }
                }
            ]
        });
    </script>
@endpush
