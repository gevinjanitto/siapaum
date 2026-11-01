@props([
    'name' => '',
    'label' => '',
    'class' => '',
    'class_input' => '',
    'uploadUrl' => '',
    'downloadUrl' => '',
    'container_id' => rand(),
    'required' => false,
    'is_show_badge' => true,
    'is_readonly' => false,
    'revisi' => '',
    'show_ttd_status' => false,
    'is_ttd' => false,
    'upload_revisi' => false,
])

<div class="form-group row mb-0 align-items-center {{ $class }}"
     id="{{ $container_id }}">
    @if ($label)
        <label class="text-primary text-xsm col-form-label fw-bold col-5 col-md-5 col-lg-4"
               for="{{ $name }}">{{ $label }}
            @if ($is_show_badge)
                @if ($required)
                    <span class="badge bg-primary fw-normal"><i class="isax isax-warning-2"></i>
                        Required</span>
                @else
                    <span class="badge bg-secondary fw-normal"><i class="isax isax-warning-2"></i>
                        Optional</span>
                @endif
            @endif
            @if ($show_ttd_status)
                @if ($is_ttd)
                    <span class="badge bg-success fw-normal"><i class="isax isax-check"></i>
                        Sudah ditandatangani</span>
                @else
                    <span class="badge bg-danger fw-normal"><i class="isax isax-warning-2"></i>
                        Belum ditandatangani</span>
                @endif
            @endif
            @if($revisi && !$upload_revisi)
                <span class="badge bg-danger fw-normal"><i class="isax isax-tick-circle"></i>
                    Revisi</span>
            @endif
            @if($upload_revisi)
                <span class="badge bg-info fw-normal"><i class="isax isax-tick-circle"></i>
                    Sudah revisi</span>
            @endif
        </label>
    @endif
    <div class="col-7 col-md-7 col-lg-8">
        <div class="row gap-2 gap-md-0">
            @if (!$is_readonly)
                <div class="col-12 col-md-{{ $downloadUrl && $uploadUrl ? 6 : ($downloadUrl || $uploadUrl ? 9 : 12) }}">
                    <input accept=".pdf,.docx"
                           aria-describedby="basic-addon1"
                           class="form-control text-xsm upload-berkas"
                           id="{{ $name }}"
                           name="{{ $name }}"
                           type="file" />
                </div>

                @if ($uploadUrl)
                    <div class="col-12 col-md-3">
                        <button class="btn btn-primary bg-primary text-xsm w-100 d-block"
                                onclick="uploadBerkasPermohonan('{{ $uploadUrl }}', '{{ csrf_token() }}', '{{ $name }}')"
                                type="button">Unggah File</button>
                    </div>
                @endif

                @if ($downloadUrl)
                    <div class="col-12 col-md-3">
                        <a class="btn-outline-primary btn text-xsm text-center w-100 d-block"
                           href="{{ $downloadUrl }}"
                           id="btn-download-{{ $name }}"
                           target="_blank">Lihat File</a>
                    </div>
                @endif
            @else
                <div class="col-12 col-md-3 offset-md-9">
                    @if ($downloadUrl)
                        <a class="btn-outline-primary btn text-xsm text-center w-100 d-block"
                           href="{{ $downloadUrl }}"
                           id="btn-download-{{ $name }}"
                           target="_blank">Lihat File</a>
                    @else
                        <span class="badge bg-warning text-xsm">Belum diunggah</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
    @if ($revisi)
        <span class="badge bg-warning w-100 d-block mt-2 fw-normal text-xsm text-wrap">Revisi terakhir: {{ $revisi }}</span>
    @endif
</div>
