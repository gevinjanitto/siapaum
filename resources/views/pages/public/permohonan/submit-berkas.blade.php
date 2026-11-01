@extends('layouts.dashboard.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row align-items-center g-2">
                <div class="col-12 col-lg-auto gap-0 order-md-1">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('public.permohonan.create') }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a> {{ $permohonan->jenisIzin->nama }}
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('public.permohonan.submit-berkas', $permohonan->id) }}"
                  method="POST">
                @csrf
                <h5 class="mt-4">Form Permohonan</h5>
                <div class="card">
                    <div class="card-body">
                        @foreach ($permohonan->formPermohonan as $form_permohonan)
                            <x-dashboard.input-inline-text :readonly=True
                                                           :required=True
                                                           class="mb-3"
                                                           id="{{ $form_permohonan->id }}"
                                                           label="{{ $form_permohonan->label }}"
                                                           name="{{ $form_permohonan->kode_isian }}"
                                                           value="{{ $form_permohonan->value }}" />
                        @endforeach
                        @if ($permohonan->is_pas_foto_required)
                            <div class="form-group row align-items-center">
                                <label class="text-primary text-xsm col-form-label fw-bold col-4 col-md-3 col-lg-2"
                                       for="pas_foto">Pas Foto</label>
                                <div class="col-8 col-md-9 col-lg-10">
                                    <img alt=""
                                         class="img-thumbnail"
                                         src="{{ Storage::url($permohonan->pas_foto_filepath) }}">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <h5 class="mt-4">Upload Berkas Permohonan</h5>
                <p class="text-xsm mb-2">Pastikan size berkas yang diunggah tidak melebihi 5MB</p>
                @foreach ($permohonan->berkasPermohonan as $berkasPermohonan)
                    <x-dashboard.input-inline-file-upload :required="$berkasPermohonan->is_required"
                                                          class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                          class_input="text-xsm"
                                                          downloadUrl="{{ $berkasPermohonan->filepath ? Storage::url($berkasPermohonan->filepath) : '' }}"
                                                          label="{{ $berkasPermohonan->nama }}"
                                                          name="{{ $berkasPermohonan->id }}"
                                                          uploadUrl="{{ route('public.permohonan.store-berkas', $permohonan->id) }}" />
                @endforeach
                <button {{ !$is_all_uploaded ? 'disabled' : '' }}
                        class="btn btn-primary bg-primary mt-3 w-100 d-block mb-4"
                        type="submit">Simpan dan Ajukan Permohonan</button>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
    <script></script>
@endpush
