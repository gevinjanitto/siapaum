@extends('layouts.dashboard.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row align-items-center g-2">
                <div class="col-12 col-lg-auto gap-0 order-md-1">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('public.reklame.create', ['nomor_registrasi' => $registrasi_reklame->nomor_registrasi]) }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a>Edit Reklame
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('public.reklame.update-reklame', ['nomor_registrasi' => encrypt($registrasi_reklame->nomor_registrasi), 'reklame' => $reklame->id]) }}"
                  enctype="multipart/form-data"
                  method="POST">
                @csrf
                @method('PUT')
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
                                                       value="{{ old($form_reklame->kode_isian, $reklame->formReklame?->firstWhere('kode_isian', $form_reklame->kode_isian)->value) }}" />
                    @elseif ($form_reklame->kode_isian == 'JENIS_REKLAME')
                        <x-dashboard.input-inline-select :options="App\Enums\JenisReklameEnum::array()"
                                                         :required=True
                                                         class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                         class_input="text-start text-xsm"
                                                         id="{{ $form_reklame->id }}"
                                                         label="{{ $form_reklame->label }}"
                                                         name="{{ $form_reklame->kode_isian }}"
                                                         placeholder="Pilih {{ $form_reklame->label }}"
                                                         value="{{ old($form_reklame->kode_isian, $reklame->formReklame?->firstWhere('kode_isian', $form_reklame->kode_isian)->value) }}" />
                    @elseif ($form_reklame->kode_isian == 'AREA_PEMASANGAN')
                        <x-dashboard.input-inline-select :options="App\Enums\AreaPemasanganReklameEnum::array()"
                                                         :required=True
                                                         class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                         class_input="text-start text-xsm"
                                                         id="{{ $form_reklame->id }}"
                                                         label="{{ $form_reklame->label }}"
                                                         name="{{ $form_reklame->kode_isian }}"
                                                         placeholder="Pilih {{ $form_reklame->label }}"
                                                         value="{{ old($form_reklame->kode_isian, $reklame->formReklame?->firstWhere('kode_isian', $form_reklame->kode_isian)->value) }}" />
                    @elseif ($form_reklame->kode_isian == 'BUNYI_REKLAME')
                        <x-dashboard.input-inline-text :required=True
                                                       class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                       class_input="text-start text-xsm"
                                                       id="{{ $form_reklame->id }}"
                                                       label="{{ $form_reklame->label }}"
                                                       name="{{ $form_reklame->kode_isian }}"
                                                       placeholder="Masukkan {{ $form_reklame->label }}"
                                                       value="{{ old($form_reklame->kode_isian, $reklame->formReklame?->firstWhere('kode_isian', $form_reklame->kode_isian)->value) }}" />
                    @else
                        <x-dashboard.input-inline-text :required=True
                                                       class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                       class_input="text-start text-xsm"
                                                       id="{{ $form_reklame->id }}"
                                                       label="{{ $form_reklame->label }}"
                                                       name="{{ $form_reklame->kode_isian }}"
                                                       placeholder="Masukkan {{ $form_reklame->label }}"
                                                       value="{{ old($form_reklame->kode_isian, $reklame->formReklame?->firstWhere('kode_isian', $form_reklame->kode_isian)->value) }}" />
                    @endif
                @endforeach
                <x-dashboard.input-inline-file accept="image/*"
                                               class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="text-start text-xsm"
                                               id="image"
                                               label="Gambar Reklame"
                                               name="image" />
                <img alt=""
                     class="img img-thumbnail"
                     src="{{ Storage::url($reklame->image_filepath) }}">
                <button class="btn btn-primary w-100 d-block mb-3 mt-5"
                        type="submit">Simpan Reklame</button>
            </form>
        </section>
    </div>
@endsection
