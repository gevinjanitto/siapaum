<div class="form-group bg-white p-2 mx-1 mb-3">
    <div class="row align-items-center g-2">
        <div class="col-12 col-lg-5">
            <label class="text-primary col-form-label fw-bold text-xsm">Ijin Terbit</label>
            @if ($permohonan->is_ttd)
                <span class="badge bg-success fw-normal"><i class="isax isax-check"></i>
                    Sudah ditandatangani</span>
            @else
                <span class="badge bg-danger fw-normal"><i class="isax isax-warning-2"></i>
                    Belum ditandatangani</span>
            @endif
        </div>
        <div class="col-12 col-lg-3 ms-lg-auto">
            @if ($permohonan->template_surat_filepath)
                <a class="btn btn-outline-primary text-xsm d-block w-100"
                   href="{{ route('download-izin-terbit', ['permohonan' => $permohonan->id, 'filepath' => md5($permohonan->template_surat_filepath ?? 0)]) }}"
                   target="_blank">
                    <i class="isax isax-download"></i> Lihat File
                </a>
            @else
                <span class="badge bg-warning">Belum diunggah</span>
            @endif
        </div>
        @if ($can_generate_ulang_izin_terbit)
            <div class="col-12 col-lg-4">
                <button class="d-block text-xsm btn w-100 btn-primary"
                        onclick="generateUlangIzinTerbit()"
                        type="button">
                    <i class="isax isax-refresh me-2"></i> Generate Ulang Ijin Terbit
                </button>
            </div>
        @endif
    </div>
</div>

@once
    @push('scripts')
        <script>
            function generateUlangIzinTerbit() {
                Swal.fire({
                    icon: 'question',
                    title: 'Apakah Anda yakin ingin generate ulang ijin terbit? Pastikan template surat pada admin telah berubah jika ingin melakukan perubahan layout surat.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.permohonan.generate-ulang-izin-terbit', $permohonan->id) }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Loading',
                                    html: 'Mohon tunggu sebentar',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading()
                                    },
                                });
                            },
                            success: function(response) {
                                Swal.close();
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Generate ulang ijin terbit berhasil'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message || 'Generate ulang ijin terbit gagal'
                                    });
                                }
                            },
                            error: function(error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: error.responseJSON?.message || 'Generate ulang ijin terbit gagal'
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Generate ulang ijin terbit dibatalkan'
                        });
                    }
                });
            }
        </script>
    @endpush
@endonce
