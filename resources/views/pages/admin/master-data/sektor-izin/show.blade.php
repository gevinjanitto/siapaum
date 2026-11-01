@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('admin.master-data.sektor-izin.index') }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a> Edit Sektor Ijin
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('admin.master-data.sektor-izin.update', $sektor_izin->id) }}"
                  enctype="multipart/form-data"
                  method="POST">
                @csrf
                @method('PUT')
                <h5 class="text-black mb-3 mt-4">Detail Sektor Ijin</h5>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-select :options="$kategori_izins->mapWithKeys(function ($kategori) {
                            return [
                                $kategori->id => $kategori->nama,
                            ];
                        })"
                                                         :required=True
                                                         class=""
                                                         id="kategori_izin_id"
                                                         label="Kategori Ijin"
                                                         name="kategori_izin_id"
                                                         placeholder="Pilih kategori ijin"
                                                         value="{{ old('kategori_izin_id', $sektor_izin->kategori_izin_id) }}" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-text :required=True
                                                       class=""
                                                       id="nama"
                                                       label="Nama"
                                                       name="nama"
                                                       placeholder="Masukkan nama sektor izin"
                                                       value="{{ old('nama', $sektor_izin->nama) }}" />
                    </div>
                </div>
                <button class="btn btn-primary w-100 d-block mb-3 fw-bold"
                        type="submit">Simpan Kategori Izin</button>
            </form>
        </section>
    </div>
@endsection
