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
                        </a> Cari Data Reklame
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <form action="{{ route('public.reklame.create') }}"
                  method="GET">
                <div class="card mb-3">
                    <div class="card-body">
                        <x-dashboard.input-inline-text :required=True
                                                       class=""
                                                       id="nomor_registrasi"
                                                       label="Nomor Registrasi"
                                                       name="nomor_registrasi"
                                                       placeholder="Masukkan nomor registrasi anda"
                                                       value="{{ old('nomor_registrasi') }}" />
                    </div>
                </div>
                <button class="btn btn-primary w-100 d-block mb-3"
                        type="submit">Cari data reklame</button>
            </form>
        </section>
    </div>
@endsection
