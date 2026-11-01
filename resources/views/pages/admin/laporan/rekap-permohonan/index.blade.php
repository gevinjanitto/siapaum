@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">Detail Permohonan</h2>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if (auth()->user()->role_id == App\Enums\RoleEnum::VERIFIKATOR->value)
                            <form action="{{ route('verifikator.laporan.rekap-permohonan.export') }}"
                                  method="POST">
                            @else
                                <form action="{{ route('admin.laporan.rekap-permohonan.export') }}"
                                      method="POST">
                        @endif
                        @csrf
                        <div class="mb-3 form-group row align-items-center bg-white p-2 mx-1 mb-3 text-xsm">
                            <label class="text-primary text-xsm col-form-label fw-bold col-4 col-md-3 col-lg-2"
                                   for="jenis_izin_id">Jenis Izin</label>
                            <div class="col-8 col-md-9 col-lg-10">
                                <x-dashboard.input-filter-select :options="$jenis_izins->map(fn($j) => $j->only('id', 'nama'))->pluck('nama', 'id')"
                                                                 allowClear="true"
                                                                 id="jenis_izin_id"
                                                                 label="Jenis Izin"
                                                                 name="jenis_izin_id"
                                                                 placeholder="Semua jenis izin" />
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3 form-group row align-items-center bg-white p-2 mx-1 mb-3 text-xsm">
                            <label class="text-primary text-xsm col-form-label fw-bold col-4 col-md-3 col-lg-2"
                                   for="status">Status</label>
                            <div class="col-8 col-md-9 col-lg-10">
                                <x-dashboard.input-filter-select :options="App\Enums\StatusPermohonanEnum::array()"
                                                                 allowClear="true"
                                                                 id="status"
                                                                 label="Status"
                                                                 name="status"
                                                                 placeholder="Semua status" />
                            </div>
                        </div>
                        <hr>
                        <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                       class_input="text-xsm"
                                                       id="periode"
                                                       label="Periode"
                                                       name="periode"
                                                       type="daterange"
                                                       value="{{ old('periode') }}" />
                        <button class="btn btn-primary mt-3 w-100 d-block"
                                type="submit">Export</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
