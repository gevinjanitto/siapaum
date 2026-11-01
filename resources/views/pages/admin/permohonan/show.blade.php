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
                        </a> Detail Permohonan Ijin
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <x-landing.progress-stepper-bar :steps=$steps />
            <x-log-aktivitas-table :permohonan=$permohonan />
            <h5>Data Diri Pemohon</h5>
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
                                           class_input="border-0 text-end text-xsm"
                                           label="Nomor Telepon"
                                           name="telepon"
                                           readonly
                                           value="{{ $permohonan->user->telepon }}" />
            <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                           class_input="border-0 text-end text-xsm"
                                           label="Memohon Untuk"
                                           name="memohon_untuk"
                                           readonly
                                           value="{{ $permohonan->memohon_untuk }}" />
            @if ($permohonan->surat_kuasa_filepath)
                <x-dashboard.input-inline-file-upload :is_readonly="true"
                                                      :is_show_badge="false"
                                                      class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                      class_input="text-xsm"
                                                      downloadUrl="{{ Storage::url($permohonan->surat_kuasa_filepath) }}"
                                                      label="Surat Kuasa"
                                                      name="surat_kuasa" />
            @endif
            <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                           class_input="border-0 text-end text-xsm"
                                           label="Nama"
                                           name="nama"
                                           readonly
                                           value="{{ $permohonan->nama }}" />
            <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                           class_input="border-0 text-end text-xsm"
                                           label="NIK"
                                           name="nik"
                                           readonly
                                           value="{{ $permohonan->nik }}" />
            <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                           class_input="border-0 text-end text-xsm"
                                           label="NPWP"
                                           name="npwp"
                                           readonly
                                           value="{{ $permohonan->npwp }}" />
            <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                           class_input="border-0 text-end text-xsm"
                                           label="Tempat Lahir"
                                           name="tempat_lahir"
                                           readonly
                                           value="{{ $permohonan->tempat_lahir }}" />
            @if ($permohonan->is_pas_foto_required || $permohonan->pas_foto_filepath)
                <div class="form-group row align-items-center bg-white p-2 mx-1 mb-3 text-xsm">
                    <label class="text-primary text-xsm col-form-label fw-bold col-4 col-md-3 col-lg-2"
                           for="pas_foto">Pas Foto</label>
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
            @endif
            <h5 class="mt-4">Data Detail Permohonan</h5>
            @foreach ($permohonan->formPermohonan as $form_permohonan)
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border-0 text-end text-xsm"
                                               label="{{ $form_permohonan->label }}"
                                               name="{{ $form_permohonan->kode_isian }}"
                                               readonly
                                               value="{{ $form_permohonan->value ?? '-' }}" />
            @endforeach
            <h5 class="mt-4">Data Berkas Permohonan</h5>
            @foreach ($permohonan->berkasPermohonan as $berkas_permohonan)
                <div class="form-group mb-3 bg-white p-2">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <label class="text-primary col-form-label fw-bold text-xsm"
                                   for="{{ $berkas_permohonan->kode_berkas }}">{{ $berkas_permohonan->nama }}
                                @if ($berkas_permohonan->is_required)
                                    <span class="badge bg-primary fw-normal"><i class="isax isax-warning-2"></i>
                                        Required</span>
                                @else
                                    <span class="badge bg-secondary fw-normal"><i class="isax isax-warning-2"></i>
                                        Optional</span>
                                @endif
                                @if ($berkas_permohonan->is_valid)
                                    <span class="badge bg-success fw-normal"><i class="isax isax-tick-circle"></i>
                                        Valid</span>
                                @else
                                    <span class="badge bg-warning fw-normal"><i class="isax isax-tick-circle"></i>
                                        Pending</span>
                                @endif
                            </label>
                        </div>
                        <div class="col-2 offset-5">
                            @if ($berkas_permohonan->filepath)
                                <a class="btn btn-outline-primary text-xsm d-block w-100"
                                   href="{{ Storage::url($berkas_permohonan->filepath) }}">
                                    <i class="isax isax-download"></i> Lihat File
                                </a>
                            @else
                                <span class="badge bg-warning">Belum diunggah</span>
                            @endif
                        </div>
                        @if ($berkas_permohonan->catatan)
                            <span class="badge bg-warning w-100 d-block mt-2 fw-normal text-xsm text-wrap">Revisi terakhir:
                                {{ $berkas_permohonan->catatan }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
            <h5 class="mt-4">Data Kelengkapan Verifikator</h5>
            @foreach ($permohonan->kelengkapanPermohonan as $kelengkapan_permohonan)
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border-0 text-end text-xsm"
                                               label="{{ $kelengkapan_permohonan->label }}"
                                               name="{{ $kelengkapan_permohonan->kode_isian }}"
                                               readonly
                                               value="{{ $kelengkapan_permohonan->value ?? '-' }}" />
            @endforeach
            <h5 class="mt-4">Data Berkas Verifikator</h5>
            {{-- <div class="form-group mb-3 bg-white p-2">
                <div class="row align-items-center">
                    <div class="col-5">
                        <label class="text-primary col-form-label fw-bold text-xsm">Surat pengantar permohonan
                            rekomendasi</label>
                    </div>
                    <div class="offset-5 col-2">
                        @if ($permohonan->surat_permohonan_rekomendasi_filepath)
                            <a class="btn btn-outline-primary text-xsm d-block w-100"
                               href="{{ Storage::url($permohonan->surat_permohonan_rekomendasi_filepath) }}">
                                <i class="isax isax-download"></i> Lihat File
                            </a>
                        @else
                            <span class="badge bg-warning">Belum diunggah</span>
                        @endif
                    </div>
                </div>
            </div> --}}
            <div class="form-group mb-3 bg-white p-2">
                <div class="row align-items-center">
                    <div class="col-5">
                        <label class="text-primary col-form-label fw-bold text-xsm">Lampiran Teknis</label>
                    </div>
                    <div class="offset-5 col-2">
                        @if ($permohonan->surat_rekomendasi_filepath)
                            <a class="btn btn-outline-primary text-xsm d-block w-100"
                               href="{{ Storage::url($permohonan->surat_rekomendasi_filepath) }}">
                                <i class="isax isax-download"></i> Lihat File
                            </a>
                        @else
                            <span class="badge bg-warning">Belum diunggah</span>
                        @endif
                    </div>
                </div>
            </div>
            @include('pages.admin.permohonan.partials.izin-terbit')
        </section>
    </div>
@endsection
