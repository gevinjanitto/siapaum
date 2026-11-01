@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">Ijin Terbit Bulanan</h2>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-5">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.laporan.ijin-terbit-bulanan.export') }}">
                            <input class="form-control"
                                   id="yearPicker"
                                   max="{{ now()->year }}"
                                   min="2021"
                                   name="tahun"
                                   placeholder="Pilih Tahun"
                                   type="number"
                                   value="{{ now()->year }}">
                            <button class="btn btn-primary mt-3 w-100 d-block"
                                    type="submit">Export</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
