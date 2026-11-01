@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('admin.master-data.kategori-izin.index') }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a> Tambah Kategori Ijin
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('admin.master-data.kategori-izin.store') }}"
                  enctype="multipart/form-data"
                  method="POST">
                @csrf
                <h5 class="text-black mb-3 mt-4">Detail Kategori Ijin</h5>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-text :required=True
                                                       class=""
                                                       id="nama"
                                                       label="Nama"
                                                       name="nama"
                                                       placeholder="Masukkan nama kategori ijin"
                                                       value="{{ old('nama') }}" />
                    </div>
                </div>
                <button class="btn btn-primary w-100 d-block mb-3 fw-bold"
                        type="submit">Simpan Kategori Izin</button>
            </form>
        </section>
    </div>
@endsection
