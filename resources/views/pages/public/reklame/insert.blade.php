@extends('layouts.dashboard.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row align-items-center g-2">
                <div class="col-12 col-lg-auto gap-0 order-md-1">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('public.reklame.index') }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a>Tambah Reklame Manual
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('public.reklame.store-reklame', encrypt($registrasi_reklame->nomor_registrasi)) }}"
                  enctype="multipart/form-data"
                  method="POST">
                @csrf
                @foreach ($form_jenis_izin as $form_reklame)
                    @if ($form_reklame->kode_isian == 'LAMA_PEMASANGAN')
                        @continue
                    @endif
                    @if ($form_reklame->tipe == 'date')
                        <x-dashboard.input-inline-text :required=True
                                                       class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                       class_input="text-start text-xsm"
                                                       id="{{ $form_reklame->id }}"
                                                       label="{{ $form_reklame->label }}"
                                                       name="{{ $form_reklame->kode_isian }}"
                                                       placeholder="Masukkan {{ $form_reklame->label }}"
                                                       type="date"
                                                       value="{{ old($form_reklame->kode_isian) }}" />
                    @elseif ($form_reklame->kode_isian == 'JENIS_REKLAME')
                        <x-dashboard.input-inline-select :options="App\Enums\JenisReklameEnum::array()"
                                                         :required=True
                                                         class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                         class_input="text-start text-xsm"
                                                         id="{{ $form_reklame->id }}"
                                                         label="{{ $form_reklame->label }}"
                                                         name="{{ $form_reklame->kode_isian }}"
                                                         placeholder="Pilih {{ $form_reklame->label }}"
                                                         value="{{ old($form_reklame->kode_isian) }}" />
                    @elseif ($form_reklame->kode_isian == 'AREA_PEMASANGAN')
                        <x-dashboard.input-inline-select :options="App\Enums\AreaPemasanganReklameEnum::array()"
                                                         :required=True
                                                         class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                         class_input="text-start text-xsm"
                                                         id="{{ $form_reklame->id }}"
                                                         label="{{ $form_reklame->label }}"
                                                         name="{{ $form_reklame->kode_isian }}"
                                                         placeholder="Pilih {{ $form_reklame->label }}"
                                                         value="{{ old($form_reklame->kode_isian) }}" />
                    @elseif ($form_reklame->kode_isian == 'BUNYI_REKLAME')
                        <x-dashboard.input-inline-textarea :required=True
                                                       class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                       class_input="text-start text-xsm"
                                                       id="{{ $form_reklame->id }}"
                                                       label="{{ $form_reklame->label }}"
                                                       name="{{ $form_reklame->kode_isian }}"
                                                       placeholder="Masukkan {{ $form_reklame->label }}"
                                                       value="{{ old($form_reklame->kode_isian) }}" />
                    @else
                        <x-dashboard.input-inline-text :required=True
                                                       class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                       class_input="text-start text-xsm"
                                                       id="{{ $form_reklame->id }}"
                                                       label="{{ $form_reklame->label }}"
                                                       name="{{ $form_reklame->kode_isian }}"
                                                       placeholder="Masukkan {{ $form_reklame->label }}"
                                                       value="{{ old($form_reklame->kode_isian) }}" />
                    @endif
                @endforeach
                <x-dashboard.input-inline-file :required=True
                                               accept="image/*"
                                               class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="text-start text-xsm"
                                               id="image"
                                               label="Gambar Reklame"
                                               name="image" />
                <button class="btn btn-primary w-100 d-block mb-3 mt-5"
                        type="submit">Simpan Reklame</button>
            </form>
        </section>
    </div>
@endsection
