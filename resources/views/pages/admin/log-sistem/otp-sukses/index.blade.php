@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">OTP Sukses</h2>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm"
                               id="permohonan-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Email</th>
                                    <th>OTP</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($otp_successes as $otp_success)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $otp_success->email }}</td>
                                        <td>{{ $otp_success->otp }}</td>
                                        <td>{{ Carbon\Carbon::parse($otp_success->created_at)->setTimezone('GMT+8')->format('d-m-Y H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
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
            $('#permohonan-table').DataTable();
        });
    </script>
@endpush
