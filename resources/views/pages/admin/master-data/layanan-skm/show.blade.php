@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('admin.master-data.layanan-skm.index') }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a> Edit Master Layanan SKM
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('admin.master-data.layanan-skm.update', $layanan_skm->id) }}"
                  enctype="multipart/form-data"
                  method="POST">
                @csrf
                @method('PUT')
                <h5 class="text-black mb-3 mt-4">Detail Layanan SKM</h5>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-select :options="$group_layanan_skms->mapWithKeys(function ($group_layanan_skm) {
                            return [$group_layanan_skm->id => $group_layanan_skm->nama];
                        })"
                                                         :required=True
                                                         class=""
                                                         id="group_layanan_skm_id"
                                                         label="Group Jenis Layanan"
                                                         name="group_layanan_skm_id"
                                                         placeholder="Pilih group jenis layanan"
                                                         value="{{ old('group_layanan_skm_id', $layanan_skm->group_layanan_skm_id) }}" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-text :required=True
                                                       class=""
                                                       id="nama"
                                                       label="Nama"
                                                       name="nama"
                                                       placeholder="Masukkan nama user"
                                                       value="{{ old('name', $layanan_skm->nama) }}" />
                    </div>
                </div>
                <button class="btn btn-primary w-100 d-block mb-3 fw-bold"
                        type="submit">Simpan Layanan SKM</button>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endpush
