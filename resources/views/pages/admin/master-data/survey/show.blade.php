@extends('layouts.dashboard.dashboard-base')


@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last d-flex align-items-center">
                    <h3 class="d-inline mb-0">
                        <a class="text-color-heading fs-4"
                           href="{{ route('admin.master-data.survey.index') }}">
                            <i class="isax-bold isax-arrow-circle-left"></i>
                        </a> Survey Khusus
                    </h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <form action="{{ route('admin.master-data.survey.update', $group_layanan_skm->id) }}"
                      enctype="multipart/form-data"
                      id="form-survey"
                      method="POST">
                    @csrf
                    @method('PUT')
                    <x-dashboard.input-inline-select :options="$layananSkm->mapWithKeys(function ($layanan) {
                        return [$layanan->id => $layanan->nama];
                    })"
                                                     :required=True
                                                     class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                     class_input="text-xsm"
                                                     id="group_layanan_skm_id"
                                                     label="Layanan"
                                                     name="group_layanan_skm_id"
                                                     placeholder="Pilih layanan"
                                                     value="{{ old('group_layanan_skm_id', $group_layanan_skm->id) }}" />
                    <h6 class="ms-2">Pertanyaan</h6>
                    <div class="pertanyaan-container">
                    </div>
                    {{-- modal add pertanyaan --}}
                    <button class="btn btn-primary btn-sm"
                            data-bs-target="#modal-add-pertanyaan"
                            data-bs-toggle="modal"
                            type="button">
                        + Tambah Pertanyaan
                    </button>

                    <div class="row mt-2 mb-3">
                        <div class="col-12">
                            <button class="btn btn-primary w-100 d-block"
                                    type="submit">Simpan</button>
                        </div>
                    </div>

                </form>
            </div>
        </section>

        {{-- modal add pertanyaan with 4 option --}}
        <div aria-hidden="true"
             aria-labelledby="modal-add-pertanyaan"
             class="modal fade"
             id="modal-add-pertanyaan"
             tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pertanyaan</h5>
                        <button aria-label="Close"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                type="button"></button>
                    </div>
                    <div class="modal-body text-start">
                        <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                       class_input="text-xsm"
                                                       label="Pertanyaan"
                                                       name="pertanyaan"
                                                       placeholder="Masukkan pertanyaan"
                                                       required />
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="ms-2">Pilihan Jawaban</h5>
                            </div>
                            <div class="col-12">
                                <div class="row">
                                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                                   class_input="text-xsm"
                                                                   label="Jawaban Poin 1"
                                                                   name="pilihan_jawaban_1"
                                                                   placeholder="Masukkan pilihan jawaban 1" />


                                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                                   class_input="text-xsm"
                                                                   label="Jawaban Poin 2"
                                                                   name="pilihan_jawaban_2"
                                                                   placeholder="Masukkan pilihan jawaban 2" />


                                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                                   class_input="text-xsm"
                                                                   label="Jawaban Poin 3"
                                                                   name="pilihan_jawaban_3"
                                                                   placeholder="Masukkan pilihan jawaban 3" />


                                    <x-dashboard.input-inline-text class="bg-white p-2 mx-1 mb-3 text-xsm"
                                                                   class_input="text-xsm"
                                                                   label="Jawaban Poin 4"
                                                                   name="pilihan_jawaban_4"
                                                                   placeholder="Masukkan pilihan jawaban 4" />

                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100 d-block"
                                        id="add-pertanyaan"
                                        type="button">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let pertanyaanContainer = $('.pertanyaan-container');
            let addPertanyaanButton = $('#add-pertanyaan');
            let modalAddPertanyaan = $('#modal-add-pertanyaan');

            addPertanyaanButton.on('click', function() {
                let pertanyaan = modalAddPertanyaan.find('input[name="pertanyaan"]').val();
                let pilihanJawaban1 = $("input[name='pilihan_jawaban_1']").val();
                let pilihanJawaban2 = $("input[name='pilihan_jawaban_2']").val();
                let pilihanJawaban3 = $("input[name='pilihan_jawaban_3']").val();
                let pilihanJawaban4 = $("input[name='pilihan_jawaban_4']").val();

                // validation
                if (!pertanyaan || !pilihanJawaban1 || !pilihanJawaban2 || !pilihanJawaban3 || !
                    pilihanJawaban4) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Semua field harus diisi!',
                    });
                    return;
                }

                // increment count
                addPertanyaan(pertanyaan, pilihanJawaban1, pilihanJawaban2, pilihanJawaban3,
                    pilihanJawaban4);

                // reset input value
                resetInput();

                modalAddPertanyaan.modal('hide');
            });

            function resetInput() {
                modalAddPertanyaan.find('input[name="pertanyaan"]').val('');
                $("input[name='pilihan_jawaban_1']").val('');
                $("input[name='pilihan_jawaban_2']").val('');
                $("input[name='pilihan_jawaban_3']").val('');
                $("input[name='pilihan_jawaban_4']").val('');
            }

            function addPertanyaan(pertanyaan, pilihanJawaban1, pilihanJawaban2, pilihanJawaban3, pilihanJawaban4) {
                // get last count. if not exist, set to 0
                let count = pertanyaanContainer.find('.pertanyaan').length;
                let pertanyaanElement = `
                    <div class="pertanyaan p-2">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-11">
                                        <div class="col-12">
                                            <p class="mb-2 fw-bold">${pertanyaan}</p>
                                        </div>
                                        <input type="hidden" name="pertanyaan[${count}][pertanyaan]" value="${pertanyaan}">
                                        <input type="hidden" name="pertanyaan[${count}][pilihan_jawaban_1]" value="${pilihanJawaban1}">
                                        <input type="hidden" name="pertanyaan[${count}][pilihan_jawaban_2]" value="${pilihanJawaban2}">
                                        <input type="hidden" name="pertanyaan[${count}][pilihan_jawaban_3]" value="${pilihanJawaban3}">
                                        <input type="hidden" name="pertanyaan[${count}][pilihan_jawaban_4]" value="${pilihanJawaban4}">
                                        <div class="row">
                                            <div class="col-3">
                                                <p class="mb-0 text-sm">1. ${pilihanJawaban1}</p>
                                            </div>
                                            <div class="col-3">
                                                <p class="mb-0 text-sm">2. ${pilihanJawaban2}</p>
                                            </div>
                                            <div class="col-3">
                                                <p class="mb-0 text-sm">3. ${pilihanJawaban3}</p>
                                            </div>
                                            <div class="col-3">
                                                <p class="mb-0 text-sm">4. ${pilihanJawaban4}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <button class="btn btn-danger btn-sm"
                                                type="button"><i class="isax delete-pertanyaan isax-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                pertanyaanContainer.append(pertanyaanElement);
            }

            pertanyaanContainer.on('click', '.delete-pertanyaan', function() {
                $(this).closest('.pertanyaan').remove();
            });


            @foreach ($group_layanan_skm->kuesionerPertanyaan as $kuesionerPertanyaan)
                addPertanyaan("{{ $kuesionerPertanyaan->pertanyaan }}",
                    @foreach ($kuesionerPertanyaan->kuesionerOpsi as $kuesionerOpsi)
                        "{{ $kuesionerOpsi->opsi }}",
                    @endforeach
                );
            @endforeach
        });

        // console log all input value inside form-survey
        $('#form-survey').on('submit', function(e) {
            e.preventDefault();
            // show confirmation swal
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data survey yang sudah disimpan tidak dapat diubah!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // submit form
                    this.submit();
                }
            });
        });
    </script>
@endpush
