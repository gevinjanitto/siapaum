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
            @if ($permohonan->jenis_izin_id != 9)
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border-0 text-end text-xsm"
                                               label="Tempat Lahir"
                                               name="tempat_lahir"
                                               readonly
                                               value="{{ $permohonan->tempat_lahir }}" />
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
            @if ($permohonan->is_pas_foto_required)
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="form-group row align-items-center justify-content-between">
                            <label class="text-primary text-xsm col-form-label fw-bold col-4 col-md-3 col-lg-2"
                                   for="pas_foto">Pas Foto 4x6</label>
                            <div class="col-8 col-md-9 col-lg-10 text-end">
                                <img alt=""
                                     class="img-thumbnail w-10"
                                     src="{{ Storage::url($permohonan->pas_foto_filepath) }}"
                                     width="200px">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <h5 class="mt-4">Data Berkas Permohonan</h5>
            @foreach ($permohonan->berkasPermohonan as $berkas_permohonan)
                <div class="form-group mb-3 bg-white p-2">
                    <div class="row align-items-center">
                        <div class="col-6 col-md-5">
                            <label class="text-primary col-form-label fw-bold text-xsm"
                                   for="{{ $berkas_permohonan->id }}">{{ $berkas_permohonan->nama }}
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
                        <div class="col-5 col-md-2 offset-md-5">
                            @if ($berkas_permohonan->filepath)
                                <a class="btn btn-outline-primary text-xsm d-block w-100"
                                   href="{{ Storage::url($berkas_permohonan->filepath) }}"
                                   target="_blank">
                                    <i class="isax isax-download"></i> Lihat File
                                </a>
                            @else
                                <span class="badge bg-warning">Belum diunggah</span>
                            @endif
                        </div>
                    </div>
                    {{-- <span class="badge bg-danger w-100 d-block mt-2 fw-normal text-xsm">Revisi terakhir: </span> --}}
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
            @if ($permohonan->jenis_izin_id == 9)
                <x-dashboard.input-inline-file-upload :is_readonly="true"
                                                      :is_show_badge="false"
                                                      class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                      class_input="text-xsm"
                                                      downloadUrl="{{ Storage::url($permohonan->reklame?->skpd_filepath) }}"
                                                      label="SKPD"
                                                      name="skpd" />
                <h5 class="mt-4">Bukti Bayar</h5>
                @if ($permohonan->alurPermohonan()->where('jenis_verifikator', App\Enums\JenisVerifikatorEnum::OPD)->where('is_done', 1)->count())
                    <x-dashboard.input-inline-file-upload :is_readonly="true"
                                                          :is_show_badge="false"
                                                          class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                          class_input="text-xsm"
                                                          downloadUrl="{{ $permohonan->reklame?->bukti_bayar_filepath ? Storage::url($permohonan->reklame->bukti_bayar_filepath) : '' }}"
                                                          label="Bukti Bayar SKPD"
                                                          name="bukti_bayar" />
                @else
                    <x-dashboard.input-inline-file-upload :is_show_badge="false"
                                                          class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                          class_input="text-xsm"
                                                          downloadUrl="{{ $permohonan->reklame?->bukti_bayar_filepath ? Storage::url($permohonan->reklame->bukti_bayar_filepath) : '' }}"
                                                          label="Bukti Bayar SKPD"
                                                          name="bukti_bayar"
                                                          uploadUrl="{{ route('public.permohonan.upload-bukti-bayar-reklame', $permohonan->id) }}" />
                @endif
            @endif
            <h5 class="mt-4">Data Berkas Verifikator</h5>
            <x-dashboard.input-inline-file-upload :is_readonly="true"
                                                  :is_show_badge="false"
                                                  :is_ttd="$permohonan->is_ttd"
                                                  :show_ttd_status="true"
                                                  class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                  class_input="text-xsm"
                                                  downloadUrl="{{ route('download-izin-terbit', ['permohonan' => $permohonan->id, 'filepath' => md5($permohonan->template_surat_filepath ?? 0)]) }}"
                                                  label="Ijin Terbit"
                                                  name="ijin_terbit" />
            @if ($need_kuesioner)
                <a class="btn fw-bold btn-primary w-100 mt-1"
                   href="{{ route('public.kuesioner.create', $permohonan->id) }}">Isi Kuesioner</a>
            @endif
        </section>
    </div>
@endsection
