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
                        </a> {{ $jenis_izin->nama }}
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <h5>Data Diri Pemohon</h5>
            <form action="{{ route('public.permohonan.submit-form', $jenis_izin->id) }}"
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
                                                 value="{{ old('memohon_untuk') }}" />
                <x-dashboard.input-inline-file class="d-none bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="text-xsm"
                                               container_id="surat_kuasa_container"
                                               label="Surat Kuasa (Jika mewakili orang lain)"
                                               name="surat_kuasa" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="text-xsm"
                                               label="Nama"
                                               name="nama"
                                               placeholder="Masukkan nama pemohon"
                                               required
                                               value="{{ old('nama') }}" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="text-xsm"
                                               label="Nomor Telepon"
                                               name="nomor_telepon"
                                               placeholder="Masukkan nomor telepon pemohon"
                                               required
                                               value="{{ old('nomor_telepon') }}" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="text-xsm"
                                               label="NIK"
                                               name="nik"
                                               placeholder="Masukkan NIK pemohon"
                                               required
                                               value="{{ old('nik') }}" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="text-xsm"
                                               label="NPWP"
                                               name="npwp"
                                               placeholder="Masukkan NPWP pemohon"
                                               required
                                               value="{{ old('npwp') }}" />
                <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                               class_input="text-xsm"
                                               label="Tempat Lahir"
                                               name="tempat_lahir"
                                               placeholder="Masukkan tempat lahir pemohon"
                                               required
                                               value="{{ old('tempat_lahir') }}" />
                @if ($jenis_izin->is_pas_foto_required)
                    <x-dashboard.input-inline-file class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="text-xsm"
                                                   label="Pas Foto 4x6"
                                                   name="pas_foto"
                                                   required />
                @endif
                <h5 class="mt-4">Data Detail Permohonan</h5>
                @foreach ($jenis_izin->formJenisIzin as $form_jenis_izin)
                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                   class_input="text-xsm"
                                                   label="{{ $form_jenis_izin->label }}"
                                                   name="{{ $form_jenis_izin->kode_isian }}"
                                                   placeholder="Masukkan {{ $form_jenis_izin->label }}"
                                                   required
                                                   type="{{ $form_jenis_izin->tipe }}"
                                                   value="{{ old($form_jenis_izin->kode_isian) }}" />
                @endforeach
                <button class="btn btn-primary bg-primary mb-5 mt-3 w-100 d-block"
                        type="submit">Simpan dan Lanjut Upload Berkas</button>
                <div class="mb-3"></div>
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
                    $('#surat_kuasa').attr('required', true);
                } else {
                    $('#surat_kuasa_container').addClass('d-none');
                    $('#surat_kuasa').attr('required', false);
                }
            });
        });

        @if (old('memohon_untuk') === '1')
            $('#surat_kuasa_container').removeClass('d-none');
            $('#surat_kuasa').attr('required', true);
        @endif
    </script>
@endpush
