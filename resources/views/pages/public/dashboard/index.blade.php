@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading fx-dash">
        <div class="fx-dash__title">
            <div>
                <span class="fx-kicker">Dashboard Pemohon</span>
                <h1>Permohonan Saya</h1>
                <p>Ajukan izin baru dan pantau setiap tahapan permohonan Anda secara langsung.</p>
            </div>
            <div class="fx-dash__title-actions">
                <a class="btn btn-primary fx-btn-pill" href="{{ route('public.permohonan.create') }}"><i class="isax isax-element-plus"></i> Pengajuan Baru</a>
                <a class="btn btn-outline-primary fx-btn-pill fw-bold" href="#" target="_blank"><i class="isax isax-document-download"></i> Download Contoh Berkas</a>
            </div>
        </div>
        <section class="section">
            <div class="card fx-table-card fx-public-list">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm"
                               id="permohonan-table">
                            <thead>
                                <tr>
                                    <th class="d-none">Permohonan</th>
                                    <th class="d-none">Step</th>
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
        $('#permohonan-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('public.permohonan.permohonan-table') }}',
                data: function(d) {
                    d.search = $('#dt-search-0').val();
                }
            },
            columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                className: 'd-flex align-items-center w-100',
                render: function(data, type, row, meta) {
                    return `
                        <div class="d-flex align-items-center gap-4">
                            <div class="flex-grow-1 ms-3">
                                <a href="${row.url}" class="text-decoration-none text-dark">
                                    <h4 class="mb-2">${row.nama_jenis_izin}</h4>
                                </a>
                                <p class="text-xsm text-primary fw-bold mb-0">${row.nomor_registrasi}</p>
                                <p class="text-xsm text-muted mb-0">Tanggal Masuk: ${row.tanggal_masuk}</p>
                                <p class="text-xsm text-muted mb-1">${row.status_badge}</p>
                                <p class="text-xsm text-primary mb-0">A/N ${row.nama}</p>
                            </div>
                            ` + (row.status == 'pending' ? `
                                <form action="${row.delete_url}" }} method="POST" id="permohonan-${row.id}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-primary" onclick="deletePermohonan(${row.id})"><i class="isax isax-trash"></i></button>
                                </form>
                                ` : '') + `
                        </div>
                    `;
                },
            }, {
                data: 'nama',
                name: 'nama',
                className: 'text-start w-100',
                orderable: false,
                render: function(data, type, row, meta) {
                    let steps = row.steps;
                    let html = '';
                    steps.forEach((step, index) => {
                        html += `
                                <div class="stepper-item ${step.is_done ? 'completed fw-bold' : ''}">
                                    <div class="step-counter ${step.is_done ? 'bg-primary text-white' : ''}">${index + 1}</div>
                                    <div class="step-name">${step.nama}</div>
                                    <p class="text-xsm text-center fw-normal">${step.done_at}</p>
                                </div>
                            `;
                    });
                    // stepper-wrapper
                    html = `<a href="${row.url}"><div class="stepper-wrapper">${html}</div></a>`;
                    return html;
                },
            }],
            // per page
            lengthMenu: [
                [5, 10, 25, 50, 100, 250, 500, -1],
                [5, 10, 25, 50, 100, 250, 500, 'All']
            ],
            // default per page
            pageLength: 5,
        });

        // $('.delete-permohonan').on('submit', function(event) {
        //     event.preventDefault();
        //     Swal.fire({
        //         title: 'Apakah Anda yakin?',
        //         text: "Data permohonan ini akan dihapus!",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'Ya, hapus data ini!'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             event.target.submit();
        //         }
        //     });
        // });

        function deletePermohonan(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data permohonan ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus data ini!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#permohonan-${id}`).submit();
                }
            });
        }
    </script>
@endpush
