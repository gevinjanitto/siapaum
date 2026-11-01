<div aria-hidden="true"
     aria-labelledby="modalValidasiBerkasLabel"
     class="modal fade"
     id="modalValidasiBerkas"
     style="display: none;"
     tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="modalValidasiBerkasLabel">Surat Lamaran</h5>
                <button aria-label="Close"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"></button>
            </div>
            <div class="modal-body">
                <div id="berkas"><iframe height="500px"
                            src=""
                            width="100%"></iframe></div>
            </div>
            <div class="modal-footer justify-content-between section-verifikasi-berkas">
                <div class="left">
                    {{-- <a class="btn btn-outline-primary"
                       download=""
                       href=""
                       id="download-berkas"
                       style="height: 50px; line-height:36px"><i class="isax isax-document-download me-2"></i>
                        Download Dokumen</a> --}}
                </div>
                <div class="right">
                    <button class="btn btn-outline-primary me-2"
                            onclick="revisiBerkas();"
                            style="height: 50px;"
                            type="button"><i class="isax isax-edit-2 me-2"></i> Revisi</button>
                    <button class="btn btn-primary"
                            onclick="submitValidBerkas();"
                            style="height: 50px;"
                            type="button"><i class="isax isax-tick-circle me-2"></i> Valid</button>
                </div>
            </div>
            <div class="modal-footer section-verifikasi-berkas d-block d-none">
                <input id="detail_berkas_id"
                       type="hidden">
                <div class="form-group mb-3">
                    <label class="form-label"
                           for=""></label>
                    <input class="form-control"
                           id="catatan_revisi_berkas"
                           name="catatan_revisi_berkas"
                           placeholder="Catatan Revisi"
                           style="height: 60px;"
                           type="text">
                </div>
                <div class="form-group text-end mt-3">
                    <button class="btn btn-outline-primary me-2 px-4"
                            onclick="batalRevisiBerkas();"
                            style="height: 50px;"
                            type="button"><i class="isax isax-close-circle me-2"></i> Batal</button>
                    <button class="btn btn-primary"
                            onclick="submitRevisiBerkas();"
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
        function lihatData(url) {
            window.open(url, '_blank');
        }

        function validasiBerkas(e) {
            var detail_id = $(e).data('detail-id');
            let url = $(e).data('url');
            let nama = $(e).data('nama');

            $('#modalValidasiBerkasLabel').html(nama);
            $('#berkas').html('<iframe src="' + url + '" width="100%" height="500px"></iframe>');
            $('#download-berkas').attr('href', url);
            $('#detail_berkas_id').val(detail_id);

            $('#modalValidasiBerkas').modal('show');
        }

        function revisiBerkas() {
            $('.section-verifikasi-berkas').toggleClass('d-none');
            $('.section-revisi-berkas').toggleClass('d-none');
        }

        function batalRevisiBerkas() {
            $('.section-verifikasi-berkas').toggleClass('d-none');
            $('.section-revisi-berkas').toggleClass('d-none');
        }

        function submitRevisiBerkas() {
            var id = $('#detail_berkas_id').val();
            var catatan_revisi = $('#catatan_revisi_berkas').val();
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
                text: "Anda akan melakukan revisi pada berkas ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, revisi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('verifikator.verifikasi.revisi-berkas') }}",
                        type: "POST",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id,
                            "catatan_revisi": catatan_revisi,
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Mohon tunggu!',
                                html: 'Sedang melakukan revisi berkas...',
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
                                'Berkas gagal direvisi.',
                                'error'
                            );
                        }
                    });
                }
            })
        }

        function submitValidBerkas() {
            var id = $('#detail_berkas_id').val();
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda akan menandai berkas ini sebagai valid!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, valid!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('verifikator.verifikasi.valid-berkas') }}",
                        type: "POST",
                        data: {
                            "_token": "{{ @csrf_token() }}",
                            "id": id,
                        },
                        // show loading
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Mohon tunggu!',
                                html: 'Sedang memproses validasi berkas...',
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
                                'Berkas gagal divalidasi.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endpush
