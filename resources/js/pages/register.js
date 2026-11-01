var countdown;

function captchaToken(containerSelector) {
    return $(containerSelector + ' textarea[name="g-recaptcha-response"]').val() || '';
}

function validateCaptcha(containerSelector) {
    if (!document.querySelector(containerSelector + ' .g-recaptcha')) {
        return true;
    }

    if (captchaToken(containerSelector)) {
        return true;
    }

    Swal.fire({
        icon: 'warning',
        title: 'Verifikasi CAPTCHA diperlukan',
        text: 'Silakan centang kotak reCAPTCHA terlebih dahulu.'
    });
    return false;
}

$('#generate_otp').click(function () {
    if (!validateInput() || !validateCaptcha('#otp-recaptcha')) {
        return;
    }

    if (countdown) {
        clearInterval(countdown);
    }

    var email = $('#email').val();

    var route_otp = $('#route_otp').val();
    var csrf_token = $('#csrf_token').val();

    $.ajax({
        url: route_otp,
        method: 'POST',
        data: {
            _token: csrf_token,
            email: email,
            'g-recaptcha-response': captchaToken('#otp-recaptcha'),
        },
        beforeSend: function () {
            // Swal loading
            Swal.fire({
                title: 'Mohon tunggu',
                html: 'Sedang mengirim kode OTP',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (response) {
            Swal.close();
            if (response.status == 'success') {
                // show modal
                $('#last_otp_at').text(response.data.last_otp_at);
                let seconds = response.data.resend_after;
                countdown = setInterval(function () {
                    seconds--;
                    // minute : second
                    let minutes = Math.floor(seconds / 60);
                    let second = seconds % 60;
                    $('#countdown').text(minutes + ':' + second);

                    if (seconds == 0) {
                        $('#countdown-container').addClass('d-none');
                        $('#resend-otp-container').addClass('d-block');
                        $('#resend-otp-container').removeClass('d-none');
                    }
                }, 1000);
                $('#staticBackdrop').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Registrasi gagal',
                    text: response.message
                });
            }
        },
        error: function (response) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Registrasi gagal',
                text: response?.responseJSON?.message ?? 'Terjadi kesalahan pada server'
            });

            $('#generate_otp').attr('disabled', false);
            $('#generate_otp').html('Daftar');
        },
    });
});

$('#resend-otp').click(function () {
    if (!validateInput() || !validateCaptcha('#otp-recaptcha')) {
        return;
    }
    if (countdown) {
        clearInterval(countdown);
    }
    var email = $('#email').val();

    var route_otp = $('#route_otp').val();
    var csrf_token = $('#csrf_token').val();

    $.ajax({
        url: route_otp,
        method: 'POST',
        data: {
            _token: csrf_token,
            email: email,
            'g-recaptcha-response': captchaToken('#otp-recaptcha'),
        },
        beforeSend: function () {
            // Swal loading
            Swal.fire({
                title: 'Mohon tunggu',
                html: 'Sedang mengirim kode OTP',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (response) {
            Swal.close();
            if (response.status == 'success') {
                $('#last_otp_at').text(response.data.last_otp_at);
                let seconds = response.data.resend_after;
                countdown = setInterval(function () {
                    seconds--;
                    // minute : second
                    let minutes = Math.floor(seconds / 60);
                    let second = seconds % 60;
                    $('#countdown').text(minutes + ':' + second);

                    if (seconds == 0) {
                        $('#countdown-container').addClass('d-none');
                        $('#resend-otp-container').addClass('d-block');
                        $('#resend-otp-container').removeClass('d-none');
                    }
                }, 1000);
                $('#countdown-container').removeClass('d-none');
                $('#countdown-container').addClass('d-block');
                $('#resend-otp-container').addClass('d-none');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Registrasi gagal',
                    text: response.message
                });
            }
        },
        error: function (response) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Registrasi gagal',
                text: response?.responseJSON?.message ?? 'Terjadi kesalahan pada server'
            });

            $('#generate_otp').attr('disabled', false);
            $('#generate_otp').html('Daftar');
        },
    });
});

$('#register').click(function () {
    var route_register = $('#route_register').val();
    var csrf_token = $('#csrf_token').val();
    var email = $('#email').val();
    var password = $('#password').val();
    var password_verify = $('#password_verify').val();
    var token = captchaToken('#register-recaptcha');
    var otp_code = '';
    document.querySelectorAll('#otp > *[id]').forEach(function (element) {
        otp_code += element.value;
    })

    $.ajax({
        url: route_register,
        method: 'POST',
        data: {
            _token: csrf_token,
            email: email,
            password: password,
            password_verify: password_verify,
            otp_code: otp_code,
            'g-recaptcha-response': token
        },
        beforeSend: function () {
            // Swal loading
            Swal.fire({
                title: 'Mohon tunggu',
                html: 'Sedang mengirim kode OTP',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (response) {
            Swal.close();
            // show swal success then redirect to login page
            if (response.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Registrasi berhasil',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(function () {
                    window.location.href = response.data.redirect;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Registrasi gagal',
                    text: response.message
                });

                if (window.grecaptcha) grecaptcha.reset()
            }
        },
        error: function (response) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Registrasi gagal',
                text: response?.responseJSON?.message ?? 'Terjadi kesalahan'
            });

            if (window.grecaptcha) grecaptcha.reset()
        },
    });
});

function validateInput() {
    var email = $('#email').val();
    var password = $('#password').val();
    var password_verify = $('#password_verify').val();

    if (email == '' || password == '' || password_verify == '') {
        Swal.fire({
            icon: 'error',
            title: 'Semua kolom harus diisi'
        });
        return false;
    }

    // verif email
    var emailRegex = /\S+@\S+\.\S+/;
    if (!emailRegex.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Format email tidak valid'
        });
        return false;
    }

    // verif password
    var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!regex.test(password)) {
        Swal.fire({
            icon: 'error',
            html: '<p class="fw-bold mb-0">Password harus terdiri dari minimal 8 karakter, 1 kapital, 1 karakter spesial, dan 1 angka</p>'
        });
        return false;
    }

    // verif password verify
    if (password != password_verify) {
        Swal.fire({
            icon: 'error',
            title: 'Password konfirmasi tidak sama'
        });
        return false;
    }

    return true;
}
