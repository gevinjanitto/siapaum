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
                        </a> Registrasi Manual
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('public.reklame.registrasi') }}"
                  method="POST">
                @csrf
               <x-dashboard.input-inline-text :required=True
                                                       class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                       class_input="text-start text-xsm"
                                                       id="nama"
                                                       label="Nama"
                                                       name="nama"
                                                       placeholder="Masukkan nama anda"
                                                       value="{{ old('nama') }}" />
                <x-dashboard.input-inline-text :required=True
                                                         class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                         class_input="text-start text-xsm"
                                                         id="nik"
                                                         label="NIK"
                                                         name="nik"
                                                         placeholder="Masukkan NIK anda"
                                                         value="{{ old('nik') }}" />
                <x-dashboard.input-inline-text :required=True
                                                            class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                            class_input="text-start text-xsm"
                                                            id="npwp"
                                                            label="NPWP"
                                                            name="npwp"
                                                            placeholder="Masukkan NPWP anda"
                                                            value="{{ old('npwp') }}" />
                <x-dashboard.input-inline-text :required=True
                                                            class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                            class_input="text-start text-xsm"
                                                            id="nama_perusahaan"
                                                            label="Nama Perusahaan"
                                                            name="nama_perusahaan"
                                                            placeholder="Masukkan nama perusahaan anda"
                                                            value="{{ old('nama_perusahaan') }}" />
                <x-dashboard.input-inline-text :required=True
                                                            class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                            class_input="text-start text-xsm"
                                                            id="alamat_perusahaan"
                                                            label="Alamat Perusahaan"
                                                            name="alamat_perusahaan"
                                                            placeholder="Masukkan alamat perusahaan anda"
                                                            value="{{ old('alamat_perusahaan') }}" />
                <x-dashboard.input-inline-text :required=True
                                                            class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                            class_input="text-start text-xsm"
                                                            id="nomor_telepon"
                                                            label="Nomor Telepon"
                                                            name="nomor_telepon"
                                                            placeholder="Masukkan nomor telepon anda"
                                                            value="{{ old('nomor_telepon') }}" />

                <button class="btn btn-primary w-100 d-block mb-3"
                        type="submit">Registrasi</button>
            </form>
        </section>
    </div>
@endsection
