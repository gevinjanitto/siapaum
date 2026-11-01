@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('admin.permohonan.index') }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a> Edit Permohonan Ijin
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <x-landing.progress-stepper-bar :steps=$steps />
            <x-log-aktivitas-table :permohonan=$permohonan />
            <h5>Data Diri Pemohon</h5>
            <form action="{{ route('admin.permohonan.update', $permohonan->id) }}"
                  enctype="multipart/form-data"
                  method="POST">
                @csrf
                @method('PUT')
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border-0 text-end text-xsm"
                                               label="No Registrasi"
                                               name="nomor_registrasi"
                                               readonly
                                               value="{{ $permohonan->nomor_registrasi }}" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border-0 text-end text-xsm"
                                               label="Status"
                                               name="status"
                                               readonly
                                               value="{{ $permohonan->status_name }}" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border-0 text-end text-xsm"
                                               label="Jenis Perijinan"
                                               name="jenis_izin"
                                               readonly
                                               value="{{ $permohonan->jenisIzin->nama }}" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border text-xsm"
                                               label="Nama"
                                               name="nama"
                                               value="{{ $permohonan->nama }}" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border text-xsm"
                                               label="NIK"
                                               name="nik"
                                               value="{{ $permohonan->nik }}" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border text-xsm"
                                               label="NPWP"
                                               name="npwp"
                                               value="{{ $permohonan->npwp }}" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border text-xsm"
                                               label="Tempat Lahir"
                                               name="tempat_lahir"
                                               value="{{ $permohonan->tempat_lahir }}" />
                @if ($permohonan->is_pas_foto_required || $permohonan->pas_foto_filepath)
                    <div class="form-group row align-items-center bg-white p-2 mx-1 mb-3 text-xsm">
                        <label class="text-primary text-xsm col-form-label fw-bold col-4 col-md-3 col-lg-2"
                               for="pas_foto">Pas Foto Saat Ini</label>
                        <div class="col-8 col-md-9 col-lg-10">
                            @if ($permohonan->pas_foto_filepath)
                                <a href="{{ Storage::url($permohonan->pas_foto_filepath) }}"
                                   target="_blank">
                                    <img alt="Pas Foto"
                                         class="img-thumbnail"
                                         src="{{ Storage::url($permohonan->pas_foto_filepath) }}"
                                         style="max-height: 180px;">
                                </a>
                            @else
                                <span class="text-muted">Belum ada pas foto</span>
                            @endif
                        </div>
                    </div>
                    <x-dashboard.input-inline-file accept="image/jpeg,image/png"
                                                   class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="border text-xsm"
                                                   label="Pas Foto Baru"
                                                   name="pas_foto" />
                    <p class="text-xsm mx-1 mb-3">Format pas foto JPG, JPEG, atau PNG. Ukuran maksimal 2MB.</p>
                @endif
                <h5 class="mt-4">Data Detail Permohonan</h5>
                @foreach ($permohonan->formPermohonan as $form_permohonan)
                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="text-xsm"
                                                   label="{{ $form_permohonan->label }}"
                                                   name="{{ $form_permohonan->kode_isian }}"
                                                   placeholder="Masukkan {{ $form_permohonan->label }}"
                                                   required
                                                   type="{{ $form_permohonan->tipe }}"
                                                   value="{{ old($form_permohonan->kode_isian, $form_permohonan->value) }}" />
                @endforeach
                <h5 class="mt-4">Data Kelengkapan Verifikator</h5>
                @foreach ($permohonan->kelengkapanPermohonan as $kelengkapan_permohonan)
                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="text-xsm"
                                                   label="{{ $kelengkapan_permohonan->label }}"
                                                   name="{{ $kelengkapan_permohonan->kode_isian }}"
                                                   placeholder="Masukkan {{ $kelengkapan_permohonan->label }}"
                                                   type="{{ $kelengkapan_permohonan->tipe }}"
                                                   value="{{ old($kelengkapan_permohonan->kode_isian, $kelengkapan_permohonan->value) }}" />
                @endforeach
                <h5 class="mt-4">Upload Berkas Permohonan</h5>
                <p class="text-xsm mb-2">Pastikan size berkas yang diunggah tidak melebihi 5MB</p>
                @foreach ($permohonan->berkasPermohonan as $berkasPermohonan)
                    <x-dashboard.input-inline-file-upload :required="$berkasPermohonan->is_required"
                                                          class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                          class_input="text-xsm"
                                                          downloadUrl="{{ $berkasPermohonan->filepath ? Storage::url($berkasPermohonan->filepath) : '' }}"
                                                          label="{{ $berkasPermohonan->nama }}"
                                                          name="{{ $berkasPermohonan->id }}"
                                                          uploadUrl="{{ route('admin.permohonan.store-berkas', $permohonan->id) }}" />
                @endforeach

                @if ($permohonan->reklame)
                    <h5 class="mt-4">Bukti Bayar SKPD Pajak Reklame</h5>
                    <x-dashboard.input-inline-file-upload :is_show_badge="false"
                                                          class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                          class_input="text-xsm"
                                                          downloadUrl="{{ $permohonan->reklame?->skpd_filepath ? Storage::url($permohonan->reklame->skpd_filepath) : '' }}"
                                                          label="SKPD"
                                                          name="skpd"
                                                          uploadUrl="{{ route('admin.permohonan.upload-skpd', $permohonan->id) }}" />
                    <x-dashboard.input-inline-file-upload :is_show_badge="false"
                                                          class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                          class_input="text-xsm"
                                                          downloadUrl="{{ $permohonan->reklame?->bukti_bayar_filepath ? Storage::url($permohonan->reklame->bukti_bayar_filepath) : '' }}"
                                                          label="Bukti Bayar SKPD"
                                                          name="bukti_bayar"
                                                          uploadUrl="{{ route('admin.permohonan.upload-bukti-bayar-reklame', $permohonan->id) }}" />
                @endif

                <h5 class="mt-4">Ijin Terbit</h5>
                @include('pages.admin.permohonan.partials.izin-terbit')

                <div class="mt-4 mb-4">
                    <button class="d-block btn w-100 btn-primary">
                        <i class="isax isax-tick-circle me-2"></i> Simpan
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
