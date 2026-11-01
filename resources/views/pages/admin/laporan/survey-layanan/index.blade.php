@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h2 class="d-inline mb-0">Survey Layanan</h2>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('admin.laporan.survey-layanan.export') }}"
                      method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <x-dashboard.input-inline-select :options="$groupLayananSkm
                                ->mapWithKeys(function ($layanan) {
                                    return [$layanan->id => $layanan->nama];
                                })
                                ->prepend('SIAP AUM', '0')"
                                                             :required=True
                                                             allowClear
                                                             class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                             class_input="text-xsm"
                                                             id="group_layanan_skm_id"
                                                             label="Layanan*"
                                                             name="group_layanan_skm_id"
                                                             placeholder="Pilih layanan"
                                                             value="{{ old('group_layanan_skm_id') }}" />
                            <hr>
                            <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                           class_input="text-xsm"
                                                           id="periode"
                                                           label="Periode"
                                                           name="periode"
                                                           type="daterange"
                                                           value="{{ old('periode') }}" />
                        </div>
                    </div>
                    <button class="btn btn-primary d-block w-100"
                            type="submit">Generate</button>
                </form>
            </div>
        </div>
    </section>
@endsection
