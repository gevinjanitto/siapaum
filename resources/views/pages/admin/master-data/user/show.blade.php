@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('admin.master-data.user.index') }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a> Edit Master User
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('admin.master-data.user.update', $user->id) }}"
                  enctype="multipart/form-data"
                  method="POST">
                @csrf
                @method('PUT')
                <h5 class="text-black mb-3 mt-4">Detail User</h5>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-text :required=True
                                                       class=""
                                                       id="nama"
                                                       label="Nama"
                                                       name="name"
                                                       placeholder="Masukkan nama user"
                                                       value="{{ old('name', $user->name) }}" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-select :options="$roles"
                                                         :required="True"
                                                         class=""
                                                         id="role_id"
                                                         label="Role"
                                                         name="role_id"
                                                         placeholder="Pilih role"
                                                         value="{{ old('role_id', $user->role_id) }}" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-text :required=True
                                                       class=""
                                                       id="telepon"
                                                       label="No Telp"
                                                       name="telepon"
                                                       placeholder="Masukkan nomor telepon"
                                                       value="{{ old('telepon', $user->telepon) }}" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-select :options="$jenis_kelamin"
                                                         :required="True"
                                                         class=""
                                                         id="jenis_kelamin"
                                                         label="Jenis Kelamin"
                                                         name="jenis_kelamin"
                                                         placeholder="Pilih jenis kelamin"
                                                         value="{{ old('jenis_kelamin', $user->jenis_kelamin) }}" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-text :required=True
                                                       class=""
                                                       id="nik"
                                                       label="NIK"
                                                       name="nik"
                                                       placeholder="Masukkan NIK"
                                                       value="{{ old('nik',$user->nik) }}" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-textarea :required=True
                                                       class=""
                                                       id="alamat"
                                                       label="Alamat"
                                                       name="alamat"
                                                       placeholder="Masukkan alamat"
                                                       value="{{ old('alamat', $user->alamat) }}" />
                    </div>
                </div>
                <h5 class="text-black mb-3 mt-4">User Akun</h5>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <x-dashboard.input-inline-text :required=True
                                                       class=""
                                                       id="email"
                                                       label="Email"
                                                       name="email"
                                                       placeholder="Masukkan email"
                                                       type="email"
                                                       value="{{ old('email', $user->email) }}" />
                    </div>
                </div>
                <button class="btn btn-primary w-100 d-block mb-3 fw-bold"
                        type="submit">Simpan User</button>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endpush
