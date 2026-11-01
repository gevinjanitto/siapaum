@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row align-items-center g-2">
                <div class="col-12 col-lg-auto gap-0">
                    <h3 class="d-inline mb-0">Kuesioner</h3>
                </div>
                <p class="d-inline mb-0">#{{ $permohonan->nomor_registrasi }}</p>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('public.kuesioner.store', $permohonan->id) }}"
                  method="post">
                @csrf
                <div class="form-group mb-4">
                    <x-dashboard.input-inline-select :options="App\Enums\PendidikanEnum::array()"
                                                     class="bg-white p-2 py-2 mb-3 rounded mx-1 text-xsm"
                                                     label="Pendidikan Terakhir"
                                                     name="pendidikan"
                                                     placeholder="Pilih pendidikan terakhir"
                                                     required
                                                     value="{{ old('pendidikan') }}" />
                    <x-dashboard.input-inline-select :options="App\Enums\JenisPekerjaanEnum::array()"
                                                     class="bg-white p-2 py-2 mb-3 rounded mx-1 text-xsm"
                                                     label="Pekerjaan"
                                                     name="pekerjaan"
                                                     placeholder="Pilih pekerjaan"
                                                     required
                                                     value="{{ old('pekerjaan') }}" />

                    <div class="card">
                        <div class="card-body pb-0">
                            @foreach ($kuesioners as $kuesioner)
                                <x-dashboard.input-survey :kuesioner=$kuesioner
                                                          :number="$loop->iteration" />
                            @endforeach
                        </div>
                    </div>
                    <button class="btn w-100 btn-primary d-block">Simpan</button>
                </div>
            </form>
        </section>
    </div>
@endsection
