@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">OTP Gagal</h2>
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
                                    <th>Exception</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($otp_faileds as $otp_failed)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $otp_failed->email }}</td>
                                        <td>{{ $otp_failed->otp }}</td>
                                        <td>{{ $otp_failed->exception }}</td>
                                        <td>{{ Carbon\Carbon::parse($otp_failed->created_at)->setTimezone('GMT+8')->format('d-m-Y H:i:s') }}</td>
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
