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
                        </a> Edit Group Layanan SKM
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('admin.master-data.layanan-skm.group-skm.update', $group_layanan_skm->id) }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <h5 class="text-black mb-3 mt-4">Detail Group Layanan SKM</h5>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-text :required=True
                                                       class=""
                                                       id="nama"
                                                       label="Nama"
                                                       name="nama"
                                                       placeholder="Masukkan nama user"
                                                       value="{{ old('name', $group_layanan_skm->nama) }}" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-file :required=True
                                                       class=""
                                                       id="image"
                                                       label="Icon Group"
                                                       name="image"
                                                       placeholder="Masukkan image group layanan skm"
                                                       value="{{ old('image') }}" />
                        @if ($group_layanan_skm->image_filepath)
                            <div class="row mt-3 align-items-center">
                                <div class="col-4 col-md-3 col-lg-2">
                                    <p class="fw-bold text-xsm text-primary d-inline">Icon Lama</p>
                                </div>
                                <div class="col-8 col-md-9 col-lg-10">
                                    <img alt="{{ $group_layanan_skm->nama }}"
                                         class="img-fluid"
                                         src="{{ Storage::url($group_layanan_skm->image_filepath) }}"
                                         style="max-width: 150px;">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
    </div>
    <button class="btn btn-primary w-100 d-block mb-3 fw-bold"
            type="submit">Simpan Group Layanan SKM</button>
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
