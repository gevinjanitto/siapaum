@extends('layouts.dashboard.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row align-items-center g-2">
                <div class="col-12 col-lg-auto gap-0 order-md-1">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('public.permohonan.create') }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a> Reklame
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="mb-3 d-flex gap-2">
                <a class="btn btn-primary"
                   href="{{ route('public.reklame.registrasi') }}"><i class="isax isax-element-plus"></i> Registrasi
                    Manual</a>
                <a class="btn btn-primary"
                   href="{{ route('public.reklame.search') }}"><i class="isax isax-document-download"></i> Import Nomor
                    Registrasi</a>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Histori registrasi</h5>
                    <div class="card-body">
                        <table class="table"
                               id="table">
                            <thead>
                                <tr>
                                    <th>Nomor Registrasi</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Alamat Perusahaan</th>
                                    <th>Tanggal Pembuatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registrasi_reklame as $registrasi)
                                    <tr>
                                        <td>{{ $registrasi->nomor_registrasi }}</td>
                                        <td>{{ $registrasi->nama_perusahaan }}</td>
                                        <td>{{ $registrasi->alamat_perusahaan }}</td>
                                        <td>{{ $registrasi->created_at->setTimezone('GMT+8')->format('d-m-Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">

                                                <form action="{{ route('public.reklame.destroy', ['nomor_registrasi' => encrypt($registrasi->nomor_registrasi)]) }}"
                                                      class="delete-registrasi-reklame"
                                                      method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger"
                                                            type="submit"><i class="isax isax-trash"></i> Hapus</button>
                                                </form>
                                                <a class="btn btn-sm btn-primary"
                                                   href="{{ route('public.reklame.create', ['nomor_registrasi' => $registrasi->nomor_registrasi]) }}"><i
                                                       class="isax isax-eye"></i> Data Reklame</a>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                "order": [
                    [3, "desc"]
                ]
            });
        });

        $('.delete-registrasi-reklame').on('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data reklame ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus data ini!'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        });
    </script>
@endpush
