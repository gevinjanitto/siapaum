@extends('layouts.dashboard.dashboard-base')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-3 align-items-center justify-content-center">
                                <img alt=""
                                     class="img-fluid"
                                     src="{{ auth()->user()->photo_profile }}">
                                <div>
                                    <p class="mb-0 fw-bold">{{ auth()->user()->nama ?? auth()->user()->email }}</p>
                                    <p class="mb-0">{{ auth()->user()->role->nama }}</p>
                                </div>
                            </div>
                            <button class="btn btn-primary"
                                    data-bs-target="#staticBackdrop"
                                    data-bs-toggle="modal"
                                    type="button">
                                Ubah Password
                            </button>
                        </div>
                        <div class="row mt-5 align-items-center justify-content-center">
                            <div class="col-12">
                                <form action="{{ route('profile.update') }}"
                                      method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group mb-4">
                                        <x-dashboard.input-inline-text label="Email"
                                                                       name="email"
                                                                       placeholder="Email"
                                                                       readonly
                                                                       value="{{ auth()->user()->email }}" />
                                    </div>
                                    <div class="form-group mb-4">
                                        <x-dashboard.input-inline-text label="Nama"
                                                                       name="name"
                                                                       placeholder="Nama"
                                                                       value="{{ old('name', auth()->user()->name) }}" />
                                    </div>
                                    <div class="form-group mb-4">
                                        <x-dashboard.input-inline-text label="NIK"
                                                                       name="nik"
                                                                       placeholder="NIK"
                                                                       value="{{ old('nik', auth()->user()->nik) }}" />
                                    </div>
                                    <div class="form-group mb-4">
                                        <x-dashboard.input-inline-select :options="['1' => 'Laki-laki', '0' => 'Perempuan']"
                                                                         class="text-xsm"
                                                                         label="Jenis Kelamin"
                                                                         name="jenis_kelamin"
                                                                         placeholder="Pilih jenis kelamin"
                                                                         value="{{ old('jenis_Kelamin', auth()->user()->jenis_kelamin) }}" />
                                    </div>
                                    <div class="form-group mb-4">
                                        <x-dashboard.input-inline-text label="No. HP"
                                                                       name="telepon"
                                                                       placeholder="No. HP"
                                                                       value="{{ old('telepon', auth()->user()->telepon) }}" />
                                    </div>
                                    <div class="form-group">
                                        <x-dashboard.input-inline-text label="Alamat"
                                                                       name="alamat"
                                                                       placeholder="Alamat"
                                                                       value="{{ old('alamat', auth()->user()->alamat) }}" />
                                    </div>
                                    <button class="d-block w-100 btn btn-primary">Simpan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div aria-hidden="true"
         aria-labelledby="staticBackdropLabel"
         class="modal fade"
         data-bs-backdrop="static"
         data-bs-keyboard="false"
         id="staticBackdrop"
         tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('update-password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="staticBackdropLabel">Ubah Password</h5>
                        <button aria-label="Close"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                type="button"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="form-group mb-4">
                            <x-landing.input-password :required=True
                                                      name="password_lama"
                                                      placeholder="Password Lama" />
                        </div>
                        <div class="form-group
                                    mb-4">
                            <x-landing.input-password label="Password Baru"
                                                      name="password_baru"
                                                      placeholder="Password Baru" />
                        </div>
                        <div class="form-group">
                            <x-landing.input-password label="Konfirmasi Password Baru"
                                                      name="konfirmasi_password_baru"
                                                      placeholder="Konfirmasi Password Baru" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary"
                                data-bs-dismiss="modal"
                                type="button">Tutup</button>
                        <button class="btn btn-primary"
                                type="submit">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
