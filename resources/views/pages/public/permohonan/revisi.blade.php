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
        <section class="section mb-3">
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
            @if (!$is_form_need_revisi)
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="border-0 text-end text-xsm"
                                               label="Memohon Untuk"
                                               readonly
                                               value="{{ $permohonan->memohon_untuk }}" />
                @if ($permohonan->is_memohon_untuk_orang_lain)
                    <x-dashboard.input-inline-file-upload :is_readonly="true"
                                                          :is_show_badge="false"
                                                          class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                          class_input="text-xsm"
                                                          downloadUrl="{{ Storage::url($permohonan->surat_kuasa_filepath) }}"
                                                          label="File Surat Kuasa"
                                                          name="surat_kuasa_old" />
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
            @else
                <form action="{{ route('public.permohonan.submit-revisi-form', $permohonan->id) }}"
                      enctype="multipart/form-data"
                      method="POST">
                    @csrf
                    <x-dashboard.input-inline-select :options="['0' => 'Diri Sendiri', '1' => 'Mewakili Orang Lain']"
                                                     class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                     class_input="text-xsm"
                                                     label="Memohon Untuk"
                                                     name="memohon_untuk"
                                                     placeholder="Pilih memohon untuk"
                                                     required
                                                     value="{{ old('memohon_untuk', $permohonan->is_memohon_untuk_orang_lain) }}" />
                    @if ($permohonan->is_memohon_untuk_orang_lain)
                        <x-dashboard.input-inline-file-upload :is_readonly="true"
                                                              :is_show_badge="false"
                                                              class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                              class_input="text-xsm"
                                                              downloadUrl="{{ Storage::url($permohonan->surat_kuasa_filepath) }}"
                                                              label="Surat Kuasa Sebelumnya"
                                                              name="surat_kuasa_old" />
                    @endif
                    <x-dashboard.input-inline-file class="d-none bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="text-xsm"
                                                   container_id="surat_kuasa_container"
                                                   label="Surat Kuasa (Jika mewakili orang lain)"
                                                   name="surat_kuasa" />
                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="text-xsm"
                                                   label="Nama"
                                                   name="nama"
                                                   placeholder="Masukkan Nama"
                                                   required
                                                   value="{{ $permohonan->nama }}" />
                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="text-xsm"
                                                   label="NIK"
                                                   name="nik"
                                                   placeholder="Masukkan NIK"
                                                   required
                                                   value="{{ $permohonan->nik }}" />
                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="text-xsm"
                                                   label="NPWP"
                                                   name="npwp"
                                                   placeholder="Masukkan NPWP"
                                                   required
                                                   value="{{ $permohonan->npwp }}" />
                    @if ($permohonan->jenis_izin_id != 9)
                        <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                       class_input="text-xsm"
                                                       label="Tempat Lahir"
                                                       name="tempat_lahir"
                                                       placeholder="Masukkan Tempat Lahir"
                                                       required
                                                       value="{{ $permohonan->tempat_lahir }}" />
                    @endif
            @endif
            <h5 class="mt-4">Data Detail Permohonan</h5>
            @if ($last_validation_form)
                <div class="mb-3">
                    @if ($last_validation_form->status == App\Enums\StatusValidasiEnum::REVISI->value)
                        <span class="badge bg-warning w-100 d-block mt-2 fw-normal text-xsm text-wrap">Revisi terakhir:
                            {{ $last_validation_form->catatan }}</span>
                    @else
                        <span class="badge bg-success w-100 d-block mt-2 fw-normal text-xsm">Valid</span>
                    @endif
                </div>
            @endif
            @foreach ($permohonan->formPermohonan as $form_permohonan)
                @if ($is_form_need_revisi)
                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="text-xsm"
                                                   label="{{ $form_permohonan->label }}"
                                                   name="{{ $form_permohonan->kode_isian }}"
                                                   placeholder="Masukkan {{ $form_permohonan->label }}"
                                                   required
                                                   type="{{ $form_permohonan->tipe }}"
                                                   value="{{ $form_permohonan->value ?? '-' }}" />
                @else
                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="border-0 text-end text-xsm"
                                                   label="{{ $form_permohonan->label }}"
                                                   name="{{ $form_permohonan->kode_isian }}"
                                                   readonly
                                                   value="{{ $form_permohonan->value ?? '-' }}" />
                @endif
            @endforeach
            @if ($permohonan->is_pas_foto_required)
                @if (!$is_form_need_revisi)
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="form-group row align-items-center justify-content-between">
                                <label class="text-primary text-xsm col-form-label fw-bold col-4 col-md-3 col-lg-2"
                                       for="pas_foto">Pas Foto 4x6</label>
                                <div class="col-8 col-md-9 col-lg-10 text-end">
                                    <img alt=""
                                         class="img-thumbnail w-10"
                                         src="{{ Storage::url($permohonan->pas_foto_filepath) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-10">
                            <div class="card mb-2">
                                <div class="card-body">
                                    <x-dashboard.input-inline-file class="bg-white text-xsm"
                                                                   class_input="text-xsm"
                                                                   label="Pas Foto 4x6 (Jika ada revisi)"
                                                                   name="pas_foto" />
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="card mb-2">
                                <div class="card-body text-center">
                                    <img alt=""
                                         src="{{ Storage::url($permohonan->pas_foto_filepath) }}"
                                         width="50px">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
            @if ($is_form_need_revisi)
                <button class="btn btn-primary btn-sm mt-3 w-100 d-block"
                        type="submit">Simpan revisi form permohonan</button>
                </form>
            @endif
            <form action="{{ route('public.permohonan.revisi', $permohonan->id) }}"
                  method="POST">
                @csrf
                <h5 class="mt-4">Data Berkas Permohonan</h5>
                @foreach ($permohonan->berkasPermohonan as $berkas_permohonan)
                    @if ($berkas_permohonan->is_revisi)
                        <x-dashboard.input-inline-file-upload :required="$berkas_permohonan->is_required"
                                                              :upload_revisi="$berkas_permohonan->validasiBerkas
                                                                  ->where('status', 'revisi')
                                                                  ->count() == 0"
                                                              class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                              class_input="text-xsm"
                                                              downloadUrl="{{ $berkas_permohonan->filepath ? Storage::url($berkas_permohonan->filepath) : '' }}"
                                                              label="{{ $berkas_permohonan->nama }}"
                                                              name="{{ $berkas_permohonan->id }}"
                                                              revisi="{{ $berkas_permohonan->catatan }}"
                                                              uploadUrl="{{ route('public.permohonan.store-berkas', $permohonan->id) }}" />
                    @else
                        <x-dashboard.input-inline-file-upload :is_readonly="true"
                                                              :required="$berkas_permohonan->is_required"
                                                              class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                              class_input="text-xsm"
                                                              downloadUrl="{{ $berkas_permohonan->filepath ? Storage::url($berkas_permohonan->filepath) : '' }}"
                                                              label="{{ $berkas_permohonan->nama }}"
                                                              name="{{ $berkas_permohonan->id }}"
                                                              uploadUrl="{{ route('public.permohonan.store-berkas', $permohonan->id) }}" />
                    @endif
                @endforeach
                <button class="btn btn-primary bg-primary mt-4 w-100 d-block"
                        type="submit">Simpan dan Lanjutkan Permohonan</button>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#memohon_untuk').on('change', function() {
                if ($(this).val() === '1') {
                    $('#surat_kuasa_container').removeClass('d-none');
                } else {
                    $('#surat_kuasa_container').addClass('d-none');
                    $('#surat_kuasa').attr('required', false);
                }
            });
        });

        @if (old('memohon_untuk', $permohonan->is_memohon_untuk_orang_lain) == '1')
            $('#surat_kuasa_container').removeClass('d-none');
        @endif
    </script>
@endpush
