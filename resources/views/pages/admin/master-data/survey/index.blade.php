@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">Survey</h2>
                    <a class="ms-4 btn btn-primary rounded"
                       href="{{ route('admin.master-data.survey.create') }}"><i class="isax isax-element-plus"></i>
                        Tambah Survey Khusus</a>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Survey Khusus</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm"
                                   id="survey-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Group</th>
                                        <th class="text-center">Total Pertanyaan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($surveys as $key=>$value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $key }}</td>
                                            <td class="text-center">{{ $value['total_pertanyaan'] }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.master-data.survey.show', $value['id']) }}"
                                                   class="btn btn-primary btn-sm rounded"><i
                                                        class="isax isax-eye"></i> Lihat</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#survey-table').DataTable();
        });
    </script>
@endpush
