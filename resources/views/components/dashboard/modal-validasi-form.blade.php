<div aria-hidden="true"
     aria-labelledby="modalValidasiFormLabel"
     class="modal fade"
     id="modalValidasiForm"
     style="display: none;"
     tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="modalValidasiFormLabel">Surat Lamaran</h5>
                <button aria-label="Close"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"></button>
            </div>
            <div class="modal-body">
                <p>Pastikan seluruh form detail permohonan yang diupload oleh pemohon telah valid</p>
            </div>
            <div class="modal-footer justify-content-between section-verifikasi-form">
                <div class="left">
                </div>
                <div class="right">
                    <button class="btn btn-outline-primary me-2"
                            onclick="revisiForm();"
                            style="height: 50px;"
                            type="button"><i class="isax isax-edit-2 me-2"></i> Revisi</button>
                    <button class="btn btn-primary"
                            onclick="submitValidForm();"
                            style="height: 50px;"
                            type="button"><i class="isax isax-tick-circle me-2"></i> Valid</button>
                </div>
            </div>
            <div class="modal-footer section-verifikasi-form d-block d-none">
                <input id="permohonan_id"
                       type="hidden">
                <div class="form-group mb-3">
                    <label class="form-label"
                           for=""></label>
                    <input class="form-control"
                           id="catatan_revisi_form"
                           name="catatan_revisi_form"
                           placeholder="Catatan Revisi"
                           style="height: 60px;"
                           type="text">
                </div>
                <div class="form-group text-end mt-3">
                    <button class="btn btn-outline-primary me-2 px-4"
                            onclick="batalRevisiForm();"
                            style="height: 50px;"
                            type="button"><i class="isax isax-close-circle me-2"></i> Batal</button>
                    <button class="btn btn-primary"
                            onclick="submitRevisiForm();"
                            style="height: 50px;"
                            type="button"><i class="isax isax-send-2 me-2"></i> Kirim
                        Revisi</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function validasiForm(e) {
            var detail_id = $(e).data('permohonan-id');
            let url = $(e).data('url');
            let nama = $(e).data('nama');

            $('#modalValidasiFormLabel').html(nama);
            $('#permohonan_id').val(detail_id);

            $('#modalValidasiForm').modal('show');
        }

        function revisiForm() {
            $('.section-verifikasi-form').toggleClass('d-none');
            $('.section-revisi-berkas').toggleClass('d-none');
        }

        function batalRevisiForm() {
            $('.section-verifikasi-form').toggleClass('d-none');
            $('.section-revisi-berkas').toggleClass('d-none');
        }

        function submitRevisiForm() {
            var id = $('#permohonan_id').val();
            var catatan_revisi = $('#catatan_revisi_form').val();
            if (catatan_revisi == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Catatan revisi tidak boleh kosong!',
                });
                return;
            }
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda akan melakukan revisi pada form ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, revisi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('verifikator.verifikasi.revisi-form') }}",
                        type: "POST",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id,
                            "catatan_revisi": catatan_revisi,
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Mohon tunggu!',
                                html: 'Sedang melakukan revisi form...',
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.status == 'success') {
                                Swal.fire(
                                    'Berhasil!',
                                    response.message,
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Gagal!',
                                'Form gagal direvisi.',
                                'error'
                            );
                        }
                    });
                }
            })
        }

        function submitValidForm() {
            var id = $('#permohonan_id').val();
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda akan menandai form ini sebagai valid!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, valid!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('verifikator.verifikasi.valid-form') }}",
                        type: "POST",
                        data: {
                            "_token": "{{ @csrf_token() }}",
                            "id": id,
                        },
                        // show loading
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Mohon tunggu!',
                                html: 'Sedang memproses validasi form...',
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.status == 'success') {
                                Swal.fire(
                                    'Berhasil!',
                                    response.message,
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Gagal!',
                                'Form gagal divalidasi.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endpush
