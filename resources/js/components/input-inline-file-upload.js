$("body").on("change", ".upload-berkas", function () {
    let berkas = $(this).prop('files')[0];
    // cek apakah type file sesuai ekstensi
    let type_file = $(this).attr('accept');
    // let type_file_berkas = get extension file
    let type_file_berkas = berkas.name.split('.').pop();
    if (type_file.indexOf(type_file_berkas) == -1) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Tipe berkas tidak sesuai, berkas harus berupa ' + type_file,
        });
        $(this).val('');
    }
});

window.uploadBerkasPermohonan = function(uploadUrl, csrf_token, berkas_key) {
    let berkas = $('#' + berkas_key).prop('files');
    if (berkas.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Berkas belum dipilih',
        });
        return;
    }else{
        berkas = berkas[0];
    }
    let form_data = new FormData();
    form_data.append('berkas', berkas);
    form_data.append('_token', csrf_token);
    form_data.append('berkas_key', berkas_key);
    $.ajax({
        url: uploadUrl,
        type: "POST",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            Swal.fire({
                title: 'Mohon tunggu',
                html: 'Sedang mengunggah berkas',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
            });
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berkas berhasil diunggah',
                    showConfirmButton: false,
                    timer: 1500
                }).then((result) => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Berkas gagal diunggah',
                    text: response.message,
                });
            }
        },
        error: function (data) {
            console.log('Error:', data);
            
            // Handle 413 Payload Too Large error
            if (data.status === 413) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file melebihi batas maksimum yang diizinkan. Pastikan ukuran file tidak melebihi 5MB.',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.responseJSON?.message || 'Terjadi kesalahan saat mengunggah berkas',
                });
            }
        }
    });
}
