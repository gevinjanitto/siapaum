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
                        </a> Reklame
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <a class="btn btn-primary mb-3"
               href="{{ route('public.reklame.create-reklame', encrypt($registrasi_reklame->nomor_registrasi)) }}">+
                Tambah data reklame manual</a>
            <div class="card">
                <div class="card-body">
                    <x-dashboard.input-inline-text :readonly=True
                                                   class="mb-3"
                                                   id="nomor_registrasi"
                                                   label="Nomor Registrasi"
                                                   name="nomor_registrasi"
                                                   value="{{ $registrasi_reklame->nomor_registrasi }}" />
                    {{-- Nama Perusahaan, Alamat Perusahaan, NPWP, NIK, Nomor Telepon --}}
                    <x-dashboard.input-inline-text :readonly=True
                                                   class="mb-3"
                                                   id="nama_perusahaan"
                                                   label="Nama Perusahaan"
                                                   name="nama_perusahaan"
                                                   value="{{ $registrasi_reklame->nama_perusahaan }}" />
                    <x-dashboard.input-inline-text :readonly=True
                                                   class="mb-3"
                                                   id="alamat_perusahaan"
                                                   label="Alamat Perusahaan"
                                                   name="alamat_perusahaan"
                                                   value="{{ $registrasi_reklame->alamat_perusahaan }}" />
                    <x-dashboard.input-inline-text :readonly=True
                                                   class="mb-3"
                                                   id="nik"
                                                   label="NIK"
                                                   name="nik"
                                                   value="{{ $registrasi_reklame->nik }}" />
                    <x-dashboard.input-inline-text :readonly=True
                                                   class="mb-3"
                                                   id="npwp"
                                                   label="NPWP"
                                                   name="npwp"
                                                   value="{{ $registrasi_reklame->npwp }}" />
                    <x-dashboard.input-inline-text :readonly=True
                                                   class="mb-3"
                                                   id="nomor_telepon"
                                                   label="Nomor Telepon"
                                                   name="nomor_telepon"
                                                   value="{{ $registrasi_reklame->nomor_telepon }}" />
                </div>
            </div>
            @if ($registrasi_reklame->reklame->count() == 0)
                <div class="alert alert-warning mt-3 fw-bold text-xsm text-center"
                     role="alert">
                    <i class="isax-bold isax-warning-2"></i> Belum terdapat data reklame yang belum diajukan pada nomor registrasi ini. Silahkan tambahkan data reklame secara manual terlebih dahulu
                </div>
            @endif
            <div class="accordion"
                 id="accordionExample">
                @foreach ($registrasi_reklame->reklame as $index => $reklame)
                    <div class="card mb-2 ">
                        <div class="card-header py-2">
                            <div class=""
                                 id="heading{{ $index }}">
                                <h5 class="mb-0">
                                    <button aria-controls="collapse{{ $index }}"
                                            aria-expanded="true"
                                            class="btn btn-secondary w-100 d-block btn-sm fw-bold"
                                            data-bs-target="#collapse{{ $index }}"
                                            data-bs-toggle="collapse"
                                            type="button">
                                        Reklame {{ $index + 1 }} <span class="fw-normal">(Klik untuk melihat
                                            detail) -
                                            {{ $reklame->is_from_sireko ? 'Data Sireko' : 'Input Manual' }}</span>
                                    </button>
                                </h5>
                            </div>

                            <div aria-labelledby="heading{{ $index }}"
                                 class="collapse"
                                 data-bs-parent="#accordionExample"
                                 id="collapse{{ $index }}">
                                <div class="card-body pb-2">
                                    @foreach ($reklame->formReklame as $form_reklame)
                                        <x-dashboard.input-inline-text :readonly=True
                                                                       :required=True
                                                                       class="mb-3"
                                                                       id="{{ $form_reklame->id }}"
                                                                       label="{{ $form_reklame->label }}"
                                                                       name="{{ $form_reklame->name }}"
                                                                       value="{{ $form_reklame->value }}" />
                                    @endforeach
                                    <img alt=""
                                         class="img img-thumbnail mb-2"
                                         src="{{ Storage::url($reklame->image_filepath) }}">
                                    @if (!$reklame->is_from_sireko)
                                        <div class="row">
                                            <div class="col-6">
                                                <a class="btn btn-secondary btn-sm d-block w-100"
                                                   href="{{ route('public.reklame.edit-reklame', ['nomor_registrasi' => encrypt($registrasi_reklame->nomor_registrasi), 'reklame' => $reklame->id]) }}"><i
                                                       class="isax isax-pen-add"></i> Edit Reklame Ini</a>
                                            </div>
                                            <div class="col-6">
                                                <form action="{{ route('public.reklame.destroy-reklame', ['nomor_registrasi' => encrypt($registrasi_reklame->nomor_registrasi), 'reklame' => $reklame->id]) }}"
                                                      class="delete-reklame"
                                                      method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-primary btn-sm d-block w-100"
                                                            onclick="deleteReklame(event)"><i class="isax isax-trash"></i>
                                                        Delete
                                                        Reklame Ini</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if ($registrasi_reklame->reklame->count() > 0)
                    <form action="{{ route('public.reklame.store', encrypt($registrasi_reklame->nomor_registrasi)) }}"
                          method="POST">
                        @csrf
                        <button class="btn btn-primary w-100 d-block mb-3 mt-5"
                                type="submit">Ajukan Permohonan</button>
                    </form>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $('.delete-reklame').on('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data reklame ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus data ini!'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        });
    </script>
@endpush
